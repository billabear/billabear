<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Invoice\InvoiceDeliveryDataMapper;
use BillaBear\DataMappers\InvoiceDataMapper;
use BillaBear\Dto\Request\App\Invoice\CreateInvoice;
use BillaBear\Dto\Response\App\Invoice\ViewInvoice;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Entity\Invoice;
use BillaBear\Event\InvoicePaid;
use BillaBear\Filters\InvoiceList;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use BillaBear\Invoice\ManualInvoiceCreator;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\InvoiceDeliveryRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use Obol\Exception\PaymentFailureException;
use Parthenon\Athena\Filters\BoolFilter;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoicesController
{
    use ValidationErrorResponseTrait;
    use CrudListTrait;
    use LoggerAwareTrait;

    #[Route('/app/invoices', name: 'app_invoices_list', methods: ['GET'])]
    public function listInvoice(
        Request $request,
        InvoiceRepositoryInterface $repository,
        SerializerInterface $serializer,
        InvoiceDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received request to list invoices');

        return $this->crudList($request, $repository, $serializer, $factory, filterList: new InvoiceList());
    }

    #[Route('/app/invoices/unpaid', name: 'app_invoices_unpaid_list', methods: ['GET'])]
    public function listUnpaidInvoice(
        Request $request,
        InvoiceRepositoryInterface $repository,
        SerializerInterface $serializer,
        InvoiceDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received request to list unpaid invoices');

        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $resultsPerPage = (int) $request->get('per_page', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $filterBuilder = new InvoiceList();
        $filters = $filterBuilder->buildFilters($request);

        // Default filter since it's list unpaid
        $filter = new BoolFilter();
        $filter->setFieldName('paid');
        $filter->setData('false');
        $filters[] = $filter;

        $resultSet = $repository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$factory, 'createQuickViewAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/invoice/{id}/download', name: 'app_invoice_download', methods: ['GET'])]
    public function downloadInvoice(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceFormatterProvider $invoiceFormatterProvider,
    ): Response {
        $this->getLogger()->info('Received request to download invoice', ['invoice_id' => $request->get('id')]);

        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $generator = $invoiceFormatterProvider->getFormatter($invoice->getCustomer());
        $pdf = $generator->generate($invoice);
        $tmpFile = tempnam('/tmp', 'pdf');
        file_put_contents($tmpFile, $pdf);

        $response = new BinaryFileResponse($tmpFile);
        $filename = $generator->filename($invoice);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/invoice/{id}/charge', name: 'app_invoice_charge', methods: ['POST'])]
    public function chargeInvoice(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceCharger $invoiceCharger,
    ): Response {
        $this->getLogger()->info('Received request to charge invoice', ['invoice_id' => $request->get('id')]);

        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $failureReason = null;
        try {
            $invoiceCharger->chargeInvoice($invoice);
        } catch (PaymentFailureException $exception) {
            $failureReason = $exception->getReason()->value;
        }

        return new JsonResponse(['paid' => $invoice->isPaid(), 'failure_reason' => $failureReason]);
    }

    #[Route('/app/invoice/{id}/view', name: 'app_invoice_view', methods: ['GET'])]
    public function viewInvoice(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataMapper $invoiceDataMapper,
        SerializerInterface $serializer,
        InvoiceDeliveryRepositoryInterface $invoiceDeliveryRepository,
        InvoiceDeliveryDataMapper $invoiceDeliveryDataMapper,
    ): Response {
        $this->getLogger()->info('Received request to view invoice', ['invoice_id' => $request->get('id')]);

        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $invoiceDeliveries = $invoiceDeliveryRepository->getForInvoice($invoice);

        $invoiceDto = $invoiceDataMapper->createAppDto($invoice);
        $dto = new ViewInvoice();
        $dto->setInvoice($invoiceDto);
        $dto->setInvoiceDeliveries(array_map([$invoiceDeliveryDataMapper, 'createAppDto'], $invoiceDeliveries));

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/invoice/{id}/paid', name: 'app_invoice_paid', methods: ['POST'])]
    public function markAsPaid(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $this->getLogger()->info('Received request to mark invoice as paid', ['invoice_id' => $request->get('id')]);

        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $invoice->setPaid(true);
        $invoice->setPaidAt(new \DateTime());
        $invoiceRepository->save($invoice);
        $eventDispatcher->dispatch(new InvoicePaid($invoice), InvoicePaid::NAME);

        return new JsonResponse(['paid' => $invoice->isPaid()]);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/invoices/create', name: 'app_invoice_create', methods: ['POST'])]
    public function createInvoice(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        InvoiceDataMapper $invoiceFactory,
        ManualInvoiceCreator $manualInvoiceCreator,
    ): Response {
        $this->getLogger()->info('Received request to create invoice');

        /** @var CreateInvoice $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateInvoice::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response) {
            return $response;
        }

        $invoice = $manualInvoiceCreator->createInvoice($dto);
        $output = $invoiceFactory->createQuickViewAppDto($invoice);
        $json = $serializer->serialize($output, 'json');

        return new JsonResponse($json, json: true);
    }
}
