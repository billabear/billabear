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

namespace App\Controller\App;

use App\Dto\Request\App\CreateSubscription;
use App\Dto\Response\App\Subscription\CreateView;
use App\Factory\PaymentDetailsFactory;
use App\Factory\SubscriptionFactory;
use App\Factory\SubscriptionPlanFactory;
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
    #[Route('/app/customer/{customerId}/subscription', name: 'app_subscription_create_view', methods: ['GET'])]
    public function createSubscriptionDetails(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionPlanFactory $subscriptionPlanFactory,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        PaymentDetailsFactory $paymentDetailsFactory,
        SerializerInterface $serializer,
        SubscriptionRepositoryInterface $subscriptionRepository,
    ): Response {
        try {
            $customer = $customerRepository->findById($request->get('customerId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionPlans = $subscriptionPlanRepository->getAll();
        $subscriptionPlanDtos = array_map([$subscriptionPlanFactory, 'createAppDto'], $subscriptionPlans);

        $subscriptions = $subscriptionRepository->getAllActiveForCustomer($customer);

        $currency = null;
        foreach ($subscriptions as $subscription) {
            $currentCurrency = $subscription->getCurrency();
            if (null !== $currentCurrency && null !== $currency && $currentCurrency !== $currency) {
                throw new \LogicException('It should not be possible for there to be active subscriptions with different currencies');
            }
            $currency = $currentCurrency;
        }

        $paymentDetails = $paymentDetailsRepository->getPaymentDetailsForCustomer($customer);
        $paymentDetailDtos = array_map([$paymentDetailsFactory, 'createAppDto'], $paymentDetails);

        $dto = new CreateView();
        $dto->setSubscriptionPlans($subscriptionPlanDtos);
        $dto->setPaymentDetails($paymentDetailDtos);
        $dto->setEligibleCurrency($currency);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/customer/{customerId}/subscription', name: 'app_subscription_create_write', methods: ['POST'])]
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

        $subscription = $subscriptionManager->startSubscriptionWithEntities($customer, $subscriptionPlan, $price, $paymentDetails, 1);
        $subscriptionDto = $subscriptionFactory->createAppDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
