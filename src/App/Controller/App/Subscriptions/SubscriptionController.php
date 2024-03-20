<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Subscriptions;

use App\Controller\App\CrudListTrait;
use App\Controller\ValidationErrorResponseTrait;
use App\Database\TransactionManager;
use App\DataMappers\CustomerDataMapper;
use App\DataMappers\PaymentDataMapper;
use App\DataMappers\PaymentMethodsDataMapper;
use App\DataMappers\PriceDataMapper;
use App\DataMappers\ProductDataMapper;
use App\DataMappers\Subscriptions\SubscriptionDataMapper;
use App\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use App\Dto\Generic\App\SubscriptionPlan;
use App\Dto\Request\App\CancelSubscription;
use App\Dto\Request\App\CreateSubscription;
use App\Dto\Request\App\Subscription\UpdatePaymentMethod;
use App\Dto\Request\App\Subscription\UpdatePlan;
use App\Dto\Request\App\Subscription\UpdatePrice;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Subscription\CreateView;
use App\Dto\Response\App\Subscription\UpdatePlanView;
use App\Dto\Response\App\Subscription\ViewSubscription;
use App\Repository\CancellationRequestRepositoryInterface;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\PaymentCardRepositoryInterface;
use App\Subscription\CancellationRequestProcessor;
use App\Subscription\PaymentMethodUpdateProcessor;
use App\User\UserProvider;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionController
{
    use ValidationErrorResponseTrait;
    use CrudListTrait;

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/customer/{customerId}/subscription', name: 'app_subscription_create_view', methods: ['GET'])]
    public function createSubscriptionDetails(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerFactory,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionPlanDataMapper $subscriptionPlanFactory,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PaymentMethodsDataMapper $paymentDetailsFactory,
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
        $schedule = null;
        foreach ($subscriptions as $subscription) {
            $currentSchedule = $subscription->getPaymentSchedule();
            $currentCurrency = $subscription->getCurrency();
            if (null !== $currentCurrency && null !== $currency && $currentCurrency !== $currency) {
                throw new \LogicException('It should not be possible for there to be active subscriptions with different currencies');
            }
            if (null !== $schedule && $currentSchedule !== $schedule) {
                throw new \LogicException('It should not be possible for there to be active subscriptions with different schedules');
            }
            $currency = $currentCurrency;
            $schedule = $currentSchedule;
        }

        $paymentDetails = $paymentDetailsRepository->getPaymentCardForCustomer($customer);
        $paymentDetailDtos = array_map([$paymentDetailsFactory, 'createAppDto'], $paymentDetails);
        $customerDto = $customerFactory->createAppDto($customer);

        $dto = new CreateView();
        $dto->setSubscriptionPlans($subscriptionPlanDtos);
        $dto->setPaymentDetails($paymentDetailDtos);
        $dto->setEligibleCurrency($currency);
        $dto->setEligibleSchedule($schedule);
        $dto->setCustomer($customerDto);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/customer/{customerId}/subscription', name: 'app_subscription_create_write', methods: ['POST'])]
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
        $paymentDetails = null;
        if ($dto->getPaymentDetails()) {
            $paymentDetails = $paymentDetailsRepository->findById($dto->getPaymentDetails());
        }
        $price = $priceRepository->findById($dto->getPrice());
        $transactionManager->start();
        try {
            $subscription = $subscriptionManager->startSubscription($customer, $subscriptionPlan, $price, $paymentDetails, $dto->getSeatNumber(), $dto->getHasTrial(), $dto->getTrialLengthDays());
        } catch (\Throwable $e) {
            $transactionManager->abort();
            throw $e;
        }
        $transactionManager->finish();
        $subscriptionDto = $subscriptionFactory->createAppDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/subscription', name: 'site_subscription_list', methods: ['GET'])]
    public function listSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        SubscriptionDataMapper $subscriptionFactory,
    ): Response {
        return $this->crudList($request, $subscriptionRepository, $serializer, $subscriptionFactory, 'updatedAt');
    }

    #[Route('/app/subscription/{subscriptionId}', name: 'app_subscription_view', methods: ['GET'])]
    public function viewSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionFactory,
        CustomerDataMapper $customerFactory,
        PaymentMethodsDataMapper $paymentDetailsFactory,
        ProductDataMapper $productFactory,
        SerializerInterface $serializer,
        PaymentRepositoryInterface $paymentRepository,
        PaymentDataMapper $paymentFactory,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            throw new NoEntityFoundException();
        }

        $dto = $subscriptionFactory->createAppDto($subscription);
        $customerDto = $customerFactory->createAppDto($subscription->getCustomer());

        $payments = $paymentRepository->getPaymentsForSubscription($subscription);
        $paymentDtos = array_map([$paymentFactory, 'createAppDto'], $payments);

        $view = new ViewSubscription();
        $view->setSubscription($dto);
        $view->setCustomer($customerDto);
        $view->setPayments($paymentDtos);
        if ($subscription->getPaymentDetails()) {
            $view->setPaymentDetails($paymentDetailsFactory->createAppDto($subscription->getPaymentDetails()));
        }
        $view->setProduct($productFactory->createAppDtoFromProduct($subscription->getPrice()->getProduct()));
        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/subscription/{subscriptionId}/payment-card', name: 'app_subscription_payment_method_update', methods: ['POST'])]
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

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/subscription/{subscriptionId}/cancel', name: 'app_subscription_cancel', methods: ['POST'])]
    public function cancelSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CancellationRequestProcessor $cancellationRequestProcessor,
        \App\DataMappers\CancellationDataMapper $cancellationRequestFactory,
        UserProvider $userProvider,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            throw new NoEntityFoundException();
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

        $user = $userProvider->getUser();

        $cancellationRequest = $cancellationRequestFactory->getCancellationRequestEntity($subscription, $dto, $user);

        $cancellationRequestRepository->save($cancellationRequest);

        try {
            $cancellationRequestProcessor->process($cancellationRequest);
        } catch (\Throwable $exception) {
            $cancellationRequestRepository->save($cancellationRequest);

            return new JsonResponse(['error' => $exception->getMessage(), 'class' => get_class($exception)], status: JsonResponse::HTTP_FAILED_DEPENDENCY);
        }

        $cancellationRequestRepository->save($cancellationRequest);

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/subscription/{subscriptionId}/price', name: 'app_app_subscription_readsubscriptionprice', methods: ['GET'])]
    public function readSubscriptionPrice(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        PriceRepositoryInterface $priceRepository,
        PriceDataMapper $priceFactory,
        SerializerInterface $serializer,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $prices = $priceRepository->getAllForProduct($subscription->getSubscriptionPlan()->getProduct());
        $active = $subscriptionRepository->getAllActiveForCustomer($subscription->getCustomer());

        if (1 !== count($active)) {
            $prices = array_filter($prices, function (Price $price) use ($subscription) {
                return $price->getSchedule() === $subscription->getPaymentSchedule() && $price->getCurrency() === $subscription->getCurrency();
            });
        }
        $dtos = array_map([$priceFactory, 'createAppDto'], $prices);
        $listResponse = new ListResponse();
        $listResponse->setHasMore(false);
        $listResponse->setData($dtos);
        $listResponse->setLastKey(null);
        $listResponse->setFirstKey(null);

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/subscription/{subscriptionId}/price', name: 'app_subscription_update_price', methods: ['POST'])]
    public function changeSubscriptionPrice(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PriceRepositoryInterface $priceRepository,
        SubscriptionManagerInterface $subscriptionManager
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var UpdatePrice $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdatePrice::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $price = $priceRepository->findById($dto->getPrice());
        $subscriptionManager->changeSubscriptionPrice($subscription, $price, BillingChangeTiming::NEXT_CYCLE);

        $subscriptionRepository->save($subscription);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/subscription/{subscriptionId}/change-plan', name: 'app_subscription_update_plan_read', methods: ['GET'])]
    public function readSubscriptionPlanAvailable(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionPlanDataMapper $subscriptionPlanFactory,
        PriceRepositoryInterface $priceRepository,
        SerializerInterface $serializer,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionCount = count($subscriptionRepository->getAllActiveForCustomer($subscription->getCustomer()));

        $subscriptionPlans = $subscriptionPlanRepository->getAll();
        $dtos = array_map([$subscriptionPlanFactory, 'createAppDto'], $subscriptionPlans);

        /** @var SubscriptionPlan $dto */
        foreach ($dtos as $dto) {
            $prices = array_filter($dto->getPrices(), function (\App\Dto\Generic\App\Price $price) use ($subscription, $subscriptionCount) {
                return (1 === $subscriptionCount || $subscription->getPaymentSchedule() === $price->getSchedule()) && $price->getCurrency() === $subscription->getCurrency();
            });
            $dto->setPrices($prices);
        }
        $viewDto = new UpdatePlanView();
        $viewDto->setPlans($dtos);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/subscription/{subscriptionId}/change-plan', name: 'app_subscription_update_plan', methods: ['POST'])]
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
