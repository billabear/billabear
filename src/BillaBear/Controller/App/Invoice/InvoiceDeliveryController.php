<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Invoice;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Invoice\InvoiceDeliverySettingsDataMapper;
use BillaBear\Dto\Request\App\Invoice\CreateInvoiceDelivery;
use BillaBear\Dto\Response\App\Invoice\InvoiceDeliveryInfo;
use BillaBear\Dto\Response\App\Invoice\InvoiceDeliveryView;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\InvoiceDeliverySettingsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceDeliveryController
{
    use LoggerAwareTrait;
    use ValidationErrorResponseTrait;

    #[Route('/app/customer/{customerId}/invoice-delivery', name: 'create_invoice_delivery', methods: ['POST'])]
    public function createNew(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        InvoiceDeliverySettingsRepositoryInterface $invoiceDeliveryRepository,
        InvoiceDeliverySettingsDataMapper $dataMapper,
    ): Response {
        $this->getLogger()->info('Received a request to create a new invoice_delivery');

        try {
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $createDto = $serializer->deserialize($request->getContent(), CreateInvoiceDelivery::class, 'json');
        $errors = $validator->validate($createDto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }

        $invoiceDelivery = $dataMapper->createEntity($createDto);
        $invoiceDelivery->setCustomer($customer);
        $invoiceDeliveryRepository->save($invoiceDelivery);

        $appDto = $dataMapper->createAppDto($invoiceDelivery);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_ACCEPTED, json: true);
    }

    #[Route('/app/customer/{customerId}/invoice-delivery/{invoiceDeliveryId}', name: 'read_edit_invoice_delivery', methods: ['GET'])]
    public function readEditExisting(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        InvoiceDeliverySettingsRepositoryInterface $invoiceDeliveryRepository,
        InvoiceDeliverySettingsDataMapper $dataMapper,
        InvoiceFormatterProvider $provider,
    ): Response {
        $this->getLogger()->info('Received a request to read an invoice_delivery');
        try {
            $invoiceDelivery = $invoiceDeliveryRepository->findById($request->get('invoiceDeliveryId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $appDto = $dataMapper->createAppDto($invoiceDelivery);
        $dto = new InvoiceDeliveryView();
        $dto->setSettings($appDto);
        $dto->setFormatters($provider->getFormattersNames());

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, json: true);
    }

    #[Route('/app/customer/{customerId}/invoice-delivery/{invoiceDeliveryId}', name: 'edit_invoice_delivery', methods: ['POST'])]
    public function editExisting(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        InvoiceDeliverySettingsRepositoryInterface $invoiceDeliveryRepository,
        InvoiceDeliverySettingsDataMapper $dataMapper,
    ): Response {
        $this->getLogger()->info('Received a request to update an invoice_delivery');

        try {
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        try {
            $invoiceDelivery = $invoiceDeliveryRepository->findById($request->get('invoiceDeliveryId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $createDto = $serializer->deserialize($request->getContent(), CreateInvoiceDelivery::class, 'json');
        $errors = $validator->validate($createDto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }

        $invoiceDelivery = $dataMapper->createEntity($createDto, $invoiceDelivery);
        $invoiceDelivery->setCustomer($customer);
        $invoiceDeliveryRepository->save($invoiceDelivery);

        $appDto = $dataMapper->createAppDto($invoiceDelivery);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_ACCEPTED, json: true);
    }

    #[Route('/app/customer/{customerId}/invoice-delivery', name: 'list_invoice_delivery', methods: ['GET'])]
    public function listInvoiceDelivery(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        InvoiceDeliverySettingsRepositoryInterface $invoiceDeliveryRepository,
        InvoiceDeliverySettingsDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received a request to list invoice_delivery');

        try {
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $invoiceDeliveries = $invoiceDeliveryRepository->getAllForCustomer($customer);
        $dtos = array_map([$dataMapper, 'createAppDto'], $invoiceDeliveries);

        $listDto = new ListResponse();
        $listDto->setData($dtos);
        $listDto->setHasMore(false);

        $json = $serializer->serialize($listDto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_ACCEPTED, json: true);
    }

    #[Route('/app/invoice-delivery', name: 'get_invoice_delivery_info', methods: ['GET'])]
    public function getInfo(
        InvoiceFormatterProvider $provider,
        SerializerInterface $serializer,
    ): Response {
        $dto = new InvoiceDeliveryInfo();
        $dto->setFormatters($provider->getFormattersNames());

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, json: true);
    }
}
