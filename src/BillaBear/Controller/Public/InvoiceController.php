<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\InvoiceDataMapper;
use BillaBear\Dto\Request\Public\ProcessPay;
use BillaBear\Dto\Response\Portal\Invoice\StripeInfo;
use BillaBear\Dto\Response\Portal\Invoice\ViewPay;
use BillaBear\Entity\Invoice;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\ManageCustomerSessionRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceController
{
    use ValidationErrorResponseTrait;

    public function __construct(private readonly LoggerInterface $controllerLogger)
    {
    }

    #[Route('/public/invoice/{id}/pay', name: 'app_public_invoice_readpay', methods: ['GET'])]
    public function readPay(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataMapper $invoiceDataMapper,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        FrontendConfig $config,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Received request to read pay for invoice', ['invoice_id' => $request->get('id')]);

        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $defaultSettings = $settingsRepository->getDefaultSettings();
        $apiKey = empty($config->getApiInfo()) ? $defaultSettings->getSystemSettings()->getStripePublicKey() : $config->getApiInfo();

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($invoice->getCustomer()));
        $stripe->setKey($apiKey);
        $viewDto = new ViewPay();
        $viewDto->setStripe($stripe);
        $viewDto->setInvoice($invoiceDataMapper->createPublicDto($invoice));

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/invoice/{id}/pay', name: 'app_public_invoice_processpay', methods: ['POST'])]
    public function processPay(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        InvoiceCharger $invoiceCharger,
    ): Response {
        $this->getLogger()->info('Received request to read pay for invoice', ['invoice_id' => $request->get('id')]);

        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $processPay = $serializer->deserialize($request->getContent(), ProcessPay::class, 'json');
        $errors = $validator->validate($processPay);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse) {
            return $errorResponse;
        }
        $paymentCard = $addCardByTokenDriver->createPaymentDetailsFromToken($invoice->getCustomer(), $processPay->getToken());
        $success = true;
        $failureReason = null;
        try {
            $invoiceCharger->chargeInvoice($invoice, $paymentCard);
        } catch (PaymentFailureException $exception) {
            $success = false;
            $failureReason = $exception->getReason()->value;
        }

        return new JsonResponse(['success' => $success, 'failure_reason' => $failureReason]);
    }

    #[Route('/public/customer/{token}/invoice/{id}/charge', name: 'billabear_public_invoice_charge_invoice', methods: ['POST'])]
    public function chargeInvoice(
        Request $request,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceCharger $invoiceCharger,
        ManageCustomerSessionRepositoryInterface $manageCustomerSessionRepository,
    ): Response {
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        try {
            $session = $manageCustomerSessionRepository->getByToken($request->get('token'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->warning('Unable to find customer management session', ['token' => $request->get('token')]);

            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }
        $this->getLogger()->info('Received an portal request to charge invoice', [
            'invoice_id' => $request->get('id'),
            'customer_id' => (string) $invoice->getCustomer()->getId(),
        ]);

        $now = new \DateTime();

        if ($session->getExpiresAt() < $now) {
            $this->getLogger()->warning(
                'Customer management Session has expired',
                [
                    'token' => $request->get('token'),
                    'customer_id' => (string) $session->getCustomer()->getId(),
                ]
            );

            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        $expiresAt = new \DateTime('+5 minutes');
        $session->setExpiresAt($expiresAt);
        $session->setUpdatedAt($now);
        $manageCustomerSessionRepository->save($session);

        $failureReason = null;
        $statusCode = Response::HTTP_OK;
        try {
            $invoiceCharger->chargeInvoice($invoice);
        } catch (PaymentFailureException $e) {
            $failureReason = $e->getReason()->value;
            $statusCode = Response::HTTP_PAYMENT_REQUIRED;
        }

        return new JsonResponse(['paid' => $invoice->isPaid(), 'failure_reason' => $failureReason], $statusCode);
    }

    #[Route('/public/customer/{token}/invoice/{id}/download', name: 'billabear_public_invoice_download_invoice', methods: ['GET'])]
    public function downloadInvoice(
        Request $request,
        ManageCustomerSessionRepositoryInterface $manageCustomerSessionRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceFormatterProvider $invoiceFormatterProvider,
    ): Response {
        try {
            /** @var Invoice $invoice */
            $invoice = $invoiceRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        try {
            $session = $manageCustomerSessionRepository->getByToken($request->get('token'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->warning('Unable to find customer management session', ['token' => $request->get('token')]);

            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }
        $this->getLogger()->info('Received an portal request to download invoice', [
            'invoice_id' => $request->get('id'),
            'customer_id' => (string) $invoice->getCustomer()->getId(),
        ]);

        $now = new \DateTime();

        if ($session->getExpiresAt() < $now) {
            $this->getLogger()->warning(
                'Customer management Session has expired',
                [
                    'token' => $request->get('token'),
                    'customer_id' => (string) $session->getCustomer()->getId(),
                ]
            );

            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        $expiresAt = new \DateTime('+5 minutes');
        $session->setExpiresAt($expiresAt);
        $session->setUpdatedAt($now);
        $manageCustomerSessionRepository->save($session);

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

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
