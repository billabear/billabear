<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\Database\TransactionManager;
use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\Dto\Request\Api\Subscription\CreateSubscription;
use BillaBear\Dto\Request\Api\Subscription\CreateTrial;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use BillaBear\Subscription\TrialManager;
use Obol\Exception\PaymentFailureException;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerSubscriptionController
{
    use LoggerAwareTrait;
    use ValidationErrorResponseTrait;

    #[Route('/api/v1/customer/{customerId}/subscription', methods: ['GET'])]
    public function listCustomerSubscriptions(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        \BillaBear\Repository\SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionFactory,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to list customer subscriptions', ['customer_id' => $request->get('customerId')]);
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

    #[Route('/api/v1/customer/{customerId}/subscription/trial', name: 'api_v1_subscription_trial', methods: ['POST'])]
    public function createTrial(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionDataMapper $subscriptionFactory,
        TrialManager $trialManager,
        FrontendAddProcessorInterface $frontendAddProcessor,
    ): Response {
        $this->getLogger()->info('Received request to create a customer trial subscription', ['customer_id' => $request->get('customerId')]);

        try {
            $customer = $customerRepository->findById($request->get('customerId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var CreateTrial $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateTrial::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }

        if ($dto->getCardToken()) {
            try {
                $frontendAddProcessor->createPaymentDetailsFromToken($customer, $dto->getCardToken());
            } catch (\Exception $exception) {
                return new JsonResponse(['error' => 'Unable to add card via token'], JsonResponse::HTTP_NOT_ACCEPTABLE);
            }
        }

        $planIdentifier = $dto->getSubscriptionPlan();
        if (Uuid::isValid($planIdentifier)) {
            $subscriptionPlan = $subscriptionPlanRepository->findById($dto->getSubscriptionPlan());
        } else {
            $subscriptionPlan = $subscriptionPlanRepository->getByCodeName($planIdentifier);
        }
        $subscription = $trialManager->startTrial($customer, $subscriptionPlan, $dto->getSeatNumber(), $dto->getTrialLengthDays());

        $subscriptionDto = $subscriptionFactory->createApiDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
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
        $this->getLogger()->info('Received request to create a customer subscriptions', ['customer_id' => $request->get('customerId')]);
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
                $this->getLogger()->info('No default payment method found');

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
            $subscription = $subscriptionManager->startSubscription($customer, $subscriptionPlan, $price, $paymentDetails, $dto->getSeatNumber());
            $transactionManager->finish();
        } catch (PaymentFailureException $e) {
            $this->getLogger()->warning('Payment failure during creation', ['reason' => $e->getReason()->value]);
            $transactionManager->abort();

            return new JsonResponse(['reason' => $e->getReason()->value], JsonResponse::HTTP_PAYMENT_REQUIRED);
        } catch (\Throwable $e) {
            $this->getLogger()->error('Error while creating subscription', ['exception_message' => $e->getMessage(), 'exception_file' => $e->getFile(), 'exception_line' => $e->getLine()]);
            $transactionManager->abort();

            return new JsonResponse([], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        $subscriptionDto = $subscriptionFactory->createApiDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }
}
