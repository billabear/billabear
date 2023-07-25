<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Api\Filters\PaymentList;
use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\CustomerDataMapper;
use App\DataMappers\PaymentFactory;
use App\DataMappers\ReceiptFactory;
use App\DataMappers\RefundFactory;
use App\DataMappers\SubscriptionFactory;
use App\Dto\Request\App\Payments\AttachToCustomer;
use App\Dto\Request\App\Payments\RefundPayment;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Payment\PaymentView;
use App\Repository\CustomerRepositoryInterface;
use App\User\UserProvider;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/payments', name: 'app_payment_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        SerializerInterface $serializer,
        PaymentFactory $paymentFactory,
    ): Response {
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

        $filterBuilder = new PaymentList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $paymentRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$paymentFactory, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/payments/{id}', name: 'app_payment_view', methods: ['GET'])]
    public function viewPayment(
        Request $request,
        PaymentRepositoryInterface $paymentRepository,
        RefundRepositoryInterface $refundRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionFactory $subscriptionFactory,
        RefundFactory $refundFactory,
        PaymentFactory $paymentFactory,
        ReceiptRepositoryInterface $receiptRepository,
        ReceiptFactory $receiptFactory,
        SerializerInterface $serializer,
    ) {
        try {
            /** @var Payment $payment */
            $payment = $paymentRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

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
        RefundFactory $refundFactory,
        SerializerInterface $serializer,
        UserProvider $userProvider,
        ValidatorInterface $validator,
    ) {
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
}
