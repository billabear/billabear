<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\Api;

use App\Api\Filters\SubscriptionList;
use App\Dto\Request\Api\Subscription\CreateSubscription;
use App\Dto\Response\Api\ListResponse;
use App\Factory\SubscriptionFactory;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionController
{
    #[Route('/api/v1/customer/{customerId}/subscription/start', name: 'api_v1_subscription_start', methods: ['POST'])]
    public function createSubscription(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionManagerInterface $subscriptionManager,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        PriceRepositoryInterface $priceRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SubscriptionFactory $subscriptionFactory,
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

        $subscriptionPlan = $subscriptionPlanRepository->findById($dto->getSubscriptionPlan());
        if ($dto->hasPaymentDetails()) {
            $paymentDetails = $paymentDetailsRepository->findById($dto->getPaymentDetails());
        } else {
            try {
                $paymentDetails = $paymentDetailsRepository->getDefaultPaymentDetailsForCustomer($customer);
            } catch (NoEntityFoundException $e) {
                return new JsonResponse(['error' => 'No default payment method'], JsonResponse::HTTP_NOT_ACCEPTABLE);
            }
        }
        $price = $priceRepository->findById($dto->getPrice());

        $subscription = $subscriptionManager->startSubscriptionWithEntities($customer, $subscriptionPlan, $price, $paymentDetails, $dto->getSeatNumbers());
        $subscriptionDto = $subscriptionFactory->createApiDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/subscription', name: 'api_v1_subscription_list', methods: ['GET'])]
    public function listSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        SubscriptionFactory $subscriptionFactory,
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
        SubscriptionFactory $subscriptionFactory,
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
}
