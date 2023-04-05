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

use App\Dto\Request\Api\Subscription\CreateSubscription;
use App\Factory\SubscriptionFactory;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
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
        $paymentDetails = $paymentDetailsRepository->findById($dto->getPaymentDetails());
        $price = $priceRepository->findById($dto->getPrice());

        $subscription = $subscriptionManager->startSubscriptionWithEntities($customer, $subscriptionPlan, $price, $paymentDetails, $dto->getSeatNumbers());
        $subscriptionDto = $subscriptionFactory->createApiDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
