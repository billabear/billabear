<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\PaymentDataMapper;
use BillaBear\DataMappers\ReceiptDataMapper;
use BillaBear\DataMappers\RefundDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\Dto\Request\App\Payments\AttachToCustomer;
use BillaBear\Dto\Request\App\Payments\RefundPayment;
use BillaBear\Dto\Response\App\Payment\PaymentView;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\User\UserProvider;
use Brick\Money\Currency;
use Brick\Money\Money;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Exception\RefundLimitExceededException;
use Parthenon\Billing\Refund\RefundManagerInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;
use Parthenon\Billing\Repository\RefundRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentController
{
    use ValidationErrorResponseTrait;
    use CrudListTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/payments', name: 'app_payment_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        SerializerInterface $serializer,
        PaymentDataMapper $paymentFactory,
    ): Response {
        $this->getLogger()->info('Received request to list payments');

        return $this->crudList($request, $paymentRepository, $serializer, $paymentFactory);
    }

    #[Route('/app/payments/{id}', name: 'app_payment_view', methods: ['GET'])]
    public function viewPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        RefundRepositoryInterface $refundRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionFactory,
        RefundDataMapper $refundFactory,
        PaymentDataMapper $paymentFactory,
        ReceiptRepositoryInterface $receiptRepository,
        ReceiptDataMapper $receiptFactory,
        SerializerInterface $serializer,
    ) {
        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }
        $this->getLogger()->info('Received request to view payment', [
            'payment_id' => $request->get('id'),
            'customer_id' => (string) $payment->getCustomer()->getId(),
        ]);

        $subscriptionDtos = array_map([$subscriptionFactory, 'createAppDto'], $payment->getSubscriptions()->toArray());
        $refunds = $refundRepository->getForPayment($payment);
        $totalRefunded = $refundRepository->getTotalRefundedForPayment($payment);
        $refundDtos = array_map([$refundFactory, 'createAppDto'], $refunds);
        $maxRefundable = $payment->getMoneyAmount()->minus($totalRefunded);
        $receipts = $receiptRepository->getForPayment($payment);
        $receiptDtos = array_map([$receiptFactory, 'createAppDto'], $receipts);

        $view = new PaymentView();
        $view->setPayment($paymentFactory->createAppDto($payment));
        $view->setRefunds($refundDtos);
        $view->setMaxRefundable($maxRefundable->getMinorAmount()->toInt());
        $view->setSubscriptions($subscriptionDtos);
        $view->setReceipts($receiptDtos);

        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/payment/{id}/attach', name: 'app_app_payment_createrefundforpayment', methods: ['POST'])]
    public function attachPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerFactory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ) {
        $this->getLogger()->info('Received request to assign payment to customer', ['payment_id' => $request->get('id')]);

        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var AttachToCustomer $dto */
        $dto = $serializer->deserialize($request->getContent(), AttachToCustomer::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $customer = $customerRepository->findById($dto->getCustomer());
        $payment->setCustomer($customer);
        $paymentRepository->save($payment);

        $customerDto = $customerFactory->createAppDto($customer);
        $json = $serializer->serialize($customerDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_ACCEPTED, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/payment/{id}/refund', name: 'app_payment_refund', methods: ['POST'])]
    public function createRefundForPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        RefundManagerInterface $refundManager,
        RefundDataMapper $refundFactory,
        SerializerInterface $serializer,
        UserProvider $userProvider,
        ValidatorInterface $validator,
    ) {
        $this->getLogger()->info('Received request to view payment', ['payment_id' => $request->get('id')]);

        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var RefundPayment $dto */
        $dto = $serializer->deserialize($request->getContent(), RefundPayment::class, 'json');

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $amount = Money::ofMinor($dto->getAmount(), Currency::of($payment->getCurrency()));
        try {
            $refund = $refundManager->issueRefundForPayment($payment, $amount, $userProvider->getUser(), $dto->getReason());
        } catch (RefundLimitExceededException $e) {
            return new JsonResponse(['message' => $e->getMessage()], status: JsonResponse::HTTP_NOT_ACCEPTABLE);
        }
        $dto = $refundFactory->createAppDto($refund);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, status: JsonResponse::HTTP_ACCEPTED, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
