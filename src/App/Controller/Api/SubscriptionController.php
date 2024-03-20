<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Api;

use App\Controller\ValidationErrorResponseTrait;
use App\Database\TransactionManager;
use App\DataMappers\CancellationDataMapper;
use App\DataMappers\Subscriptions\SubscriptionDataMapper;
use App\Dto\Request\Api\Subscription\CancelSubscription;
use App\Dto\Request\Api\Subscription\CreateSubscription;
use App\Dto\Request\Api\Subscription\UpdatePlan;
use App\Dto\Request\App\Subscription\UpdatePaymentMethod;
use App\Dto\Response\Api\ListResponse;
use App\Filters\SubscriptionList;
use App\Repository\CancellationRequestRepositoryInterface;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\PaymentCardRepositoryInterface;
use App\Subscription\CancellationRequestProcessor;
use App\Subscription\PaymentMethodUpdateProcessor;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionController
{
    use ValidationErrorResponseTrait;
    use LoggerAwareTrait;

    public function __construct(private CancellationDataMapper $cancellationRequestFactory)
    {
    }

    #[Route('/api/v1/customer/{customerId}/subscription', methods: ['GET'])]
    public function listCustomerSubscriptions(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        \App\Repository\SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionFactory,
        SerializerInterface $serializer,
    ) {
        try {
            $customer = $customerRepository->findById($request->get('customerId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptions = $subscriptionRepository->getAllActiveForCustomer($customer);
        $dtos = array_map([$subscriptionFactory, 'createAppDto'], $subscriptions);

        $listResponse = new ListResponse();
        $listResponse->setHasMore(false);
        $listResponse->setData($dtos);
        $listResponse->setLastKey(null);

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/customer/{customerId}/subscription/start', name: 'api_v1_subscription_start', methods: ['POST'])]
    public function createSubscription(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionManagerInterface $subscriptionManager,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PriceRepositoryInterface $priceRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SubscriptionDataMapper $subscriptionFactory,
        TransactionManager $transactionManager,
        FrontendAddProcessorInterface $frontendAddProcessor,
    ): Response {
        try {
            $customer = $customerRepository->findById($request->get('customerId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var CreateSubscription $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateSubscription::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $planIdentifier = $dto->getSubscriptionPlan();
        if (Uuid::isValid($planIdentifier)) {
            $subscriptionPlan = $subscriptionPlanRepository->findById($dto->getSubscriptionPlan());
        } else {
            $subscriptionPlan = $subscriptionPlanRepository->getByCodeName($planIdentifier);
        }

        if ($dto->getCardToken()) {
            try {
                $paymentDetails = $frontendAddProcessor->createPaymentDetailsFromToken($customer, $dto->getCardToken());
            } catch (\Exception $exception) {
                return new JsonResponse(['error' => 'Unable to add card via token'], JsonResponse::HTTP_NOT_ACCEPTABLE);
            }
        } elseif ($dto->hasPaymentDetails()) {
            $paymentDetails = $paymentDetailsRepository->findById($dto->getPaymentDetails());
        } else {
            try {
                $paymentDetails = $paymentDetailsRepository->getDefaultPaymentCardForCustomer($customer);
            } catch (NoEntityFoundException $e) {
                return new JsonResponse(['error' => 'No default payment method'], JsonResponse::HTTP_NOT_ACCEPTABLE);
            }
        }
        if ($dto->getPrice()) {
            $price = $priceRepository->findById($dto->getPrice());
        } else {
            $price = $subscriptionPlan->getPriceForCurrencyAndSchedule($dto->getCurrency(), $dto->getSchedule());
        }
        $transactionManager->start();
        try {
            $subscription = $subscriptionManager->startSubscription($customer, $subscriptionPlan, $price, $paymentDetails, $dto->getSeatNumbers());
            $transactionManager->finish();
        } catch (PaymentFailureException $e) {
            $this->getLogger()->warning('Payment failure during creation', ['exception_message' => $e->getMessage()]);
            $transactionManager->abort();

            return new JsonResponse([], JsonResponse::HTTP_PAYMENT_REQUIRED);
        } catch (\Throwable $e) {
            $this->getLogger()->error('Error while creating subscription', ['exception_message' => $e->getMessage()]);
            $transactionManager->abort();

            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        $subscriptionDto = $subscriptionFactory->createApiDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/subscription', name: 'api_v1_subscription_list', methods: ['GET'])]
    public function listSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        SubscriptionDataMapper $subscriptionFactory,
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

        $filterBuilder = new SubscriptionList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $subscriptionRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$subscriptionFactory, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/subscription/{id}', name: 'api_v1_subscription_view', methods: ['GET'])]
    public function viewSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        SubscriptionDataMapper $subscriptionFactory,
    ): Response {
        try {
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(null, status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $subscriptionFactory->createAppDto($subscription);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/subscription/{id}/cancel', name: 'api_v1_subscription_cancel', methods: ['POST'])]
    public function cancelSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        CancellationRequestProcessor $cancellationRequestProcessor,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        CancellationDataMapper $cancellationRequestFactory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        try {
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(null, status: JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var CancelSubscription $dto */
        $dto = $serializer->deserialize($request->getContent(), CancelSubscription::class, 'json');
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

        $cancellationRequest = $cancellationRequestFactory->getCancellationRequestEntity($subscription, $dto);

        $cancellationRequestRepository->save($cancellationRequest);

        try {
            $cancellationRequestProcessor->process($cancellationRequest);
        } catch (\Throwable $exception) {
            $cancellationRequestRepository->save($cancellationRequest);

            // return new JsonResponse(['error' => $exception->getMessage(), 'class' => get_class($exception)], status: JsonResponse::HTTP_FAILED_DEPENDENCY);
        }

        $cancellationRequestRepository->save($cancellationRequest);

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/subscription/{subscriptionId}/payment-method', name: 'api_v1_subscription_payment_method_update', methods: ['PUT'])]
    public function updatePaymentMethod(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PaymentMethodUpdateProcessor $methodUpdateProcessor,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            throw new NoEntityFoundException();
        }

        /** @var UpdatePaymentMethod $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdatePaymentMethod::class, 'json');
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

        $paymentDetails = $paymentDetailsRepository->findById($dto->getPaymentDetails());
        $methodUpdateProcessor->process($subscription, $paymentDetails);

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/subscription/{subscriptionId}/plan', name: 'api_v1_subscription_update_plan', methods: ['POST'])]
    public function changeSubscriptionPlan(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        PriceRepositoryInterface $priceRepository,
        SubscriptionManagerInterface $subscriptionManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var UpdatePlan $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdatePlan::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }
        $change = match ($dto->getWhen()) {
            UpdatePlan::WHEN_INSTANTLY => BillingChangeTiming::INSTANTLY,
            default => BillingChangeTiming::NEXT_CYCLE,
        };

        $price = $priceRepository->findById($dto->getPriceId());
        $subscriptionPlan = $subscriptionPlanRepository->findById($dto->getPlanId());
        $subscriptionManager->changeSubscriptionPlan($subscription, $subscriptionPlan, $price, $change);

        $subscriptionRepository->save($subscription);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}
