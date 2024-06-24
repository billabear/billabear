<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\Database\TransactionManager;
use BillaBear\DataMappers\CancellationDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\Dto\Request\Api\Subscription\CancelSubscription;
use BillaBear\Dto\Request\Api\Subscription\ChangePrice;
use BillaBear\Dto\Request\Api\Subscription\ExtendTrial;
use BillaBear\Dto\Request\Api\Subscription\UpdatePlan;
use BillaBear\Dto\Request\App\Subscription\UpdatePaymentMethod;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Filters\SubscriptionList;
use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use BillaBear\Subscription\CancellationRequestProcessor;
use BillaBear\Subscription\PaymentMethodUpdateProcessor;
use BillaBear\Subscription\TrialManager;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionController
{
    use ValidationErrorResponseTrait;
    use LoggerAwareTrait;

    public function __construct(private CancellationDataMapper $cancellationRequestFactory)
    {
    }

    #[Route('/api/v1/subscription', name: 'api_v1_subscription_list', methods: ['GET'])]
    public function listSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        SubscriptionDataMapper $subscriptionFactory,
    ): Response {
        $this->getLogger()->info('Received request to list all subscriptions');
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

        $dtos = array_map([$subscriptionFactory, 'createApiDto'], $resultSet->getResults());
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
        $this->getLogger()->info('Received request to view subscription', ['subscription_id' => $request->get('id')]);
        try {
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(null, status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $subscriptionFactory->createApiDto($subscription);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/subscription/{id}/extend', name: 'api_v1_subscription_extend', methods: ['POST'])]
    public function extendSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        PriceRepositoryInterface $priceRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        TransactionManager $transactionManager,
        TrialManager $trialManager,
        SubscriptionDataMapper $subscriptionDataMapper,
    ): Response {
        $this->getLogger()->info('Received request to extend subscription', ['subscription_id' => $request->get('id')]);
        try {
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(null, status: JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var ExtendTrial $dto */
        $dto = $serializer->deserialize($request->getContent(), ExtendTrial::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }

        $transactionManager->start();
        try {
            $price = $priceRepository->findById($dto->getPrice());
            $trialManager->extendTrial($subscription, $price);
        } catch (PaymentFailureException $e) {
            $this->getLogger()->warning('Payment failure during extension', ['reason' => $e->getReason()->value]);
            $transactionManager->abort();

            return new JsonResponse(['reason' => $e->getReason()->value], JsonResponse::HTTP_PAYMENT_REQUIRED);
        } catch (\Throwable $e) {
            $this->getLogger()->error('Error while extending subscription', ['exception_message' => $e->getMessage(), 'exception_file' => $e->getFile(), 'exception_line' => $e->getLine()]);
            $transactionManager->abort();

            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        $transactionManager->finish();
        $outputDto = $subscriptionDataMapper->createApiDto($subscription);
        $json = $serializer->serialize($outputDto, 'json');

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
        $this->getLogger()->info('Received request to cancel subscriptions', ['subscription_id' => $request->get('id')]);
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
        $this->getLogger()->info('Received request to update payment method for subscriptions', ['subscription_id' => $request->get('subscriptionId')]);
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
        $this->getLogger()->info('Received request to change subscription plan', ['subscription_id' => $request->get('subscriptionId')]);
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

        $price = $priceRepository->findById($dto->getPrice());
        $subscriptionPlan = $subscriptionPlanRepository->findById($dto->getPlan());
        $subscriptionManager->changeSubscriptionPlan($subscription, $subscriptionPlan, $price, $change);

        $subscriptionRepository->save($subscription);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/subscription/{subscriptionId}/price', name: 'api_v1_subscription_update_price', methods: ['POST'])]
    public function changeSubscriptionPrice(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        PriceRepositoryInterface $priceRepository,
        SubscriptionManagerInterface $subscriptionManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        $this->getLogger()->info('Received a request to api change price of subscription', ['subscription_id' => $request->get('subscriptionId')]);
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var ChangePrice $dto */
        $dto = $serializer->deserialize($request->getContent(), ChangePrice::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $price = $priceRepository->findById($dto->getPrice());
        $when = BillingChangeTiming::from($dto->getWhen());
        $subscriptionManager->changeSubscriptionPrice($subscription, $price, $when);

        $subscriptionRepository->save($subscription);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_ACCEPTED);
    }
}
