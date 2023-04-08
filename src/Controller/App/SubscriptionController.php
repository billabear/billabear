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

use App\Api\Filters\SubscriptionList;
use App\Customer\CustomerFactory;
use App\Dto\Request\App\CancelSubscription;
use App\Dto\Request\App\CreateSubscription;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Subscription\CreateView;
use App\Dto\Response\App\Subscription\ViewSubscription;
use App\Entity\CancellationRequest;
use App\Factory\PaymentDetailsFactory;
use App\Factory\ProductFactory;
use App\Factory\SubscriptionFactory;
use App\Factory\SubscriptionPlanFactory;
use App\Repository\CancellationRequestRepositoryInterface;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Entity\BillingAdminInterface;
use Parthenon\Billing\Entity\Subscription;
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
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

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

        $paymentDetails = $paymentDetailsRepository->getPaymentDetailsForCustomer($customer);
        $paymentDetailDtos = array_map([$paymentDetailsFactory, 'createAppDto'], $paymentDetails);

        $dto = new CreateView();
        $dto->setSubscriptionPlans($subscriptionPlanDtos);
        $dto->setPaymentDetails($paymentDetailDtos);
        $dto->setEligibleCurrency($currency);
        $dto->setEligibleSchedule($schedule);

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

        $subscription = $subscriptionManager->startSubscription($customer, $subscriptionPlan, $price, $paymentDetails, $dto->getSeatNumbers());
        $subscriptionDto = $subscriptionFactory->createAppDto($subscription);
        $json = $serializer->serialize($subscriptionDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/subscription', name: 'site_subscription_list', methods: ['GET'])]
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
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/subscription/{subscriptionId}', name: 'app_subscription_view', methods: ['GET'])]
    public function viewSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionFactory $subscriptionFactory,
        CustomerFactory $customerFactory,
        ProductFactory $productFactory,
        SerializerInterface $serializer
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            throw new NoEntityFoundException();
        }

        $dto = $subscriptionFactory->createAppDto($subscription);
        $customerDto = $customerFactory->createAppDtoFromCustomer($subscription->getCustomer());
        $view = new ViewSubscription();
        $view->setSubscription($dto);
        $view->setCustomer($customerDto);
        $view->setProduct($productFactory->createAppDtoFromProduct($subscription->getSubscriptionPlan()->getProduct()));
        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/subscription/{subscriptionId}/cancel', name: 'app_subscription_cancel', methods: ['POST'])]
    public function cancelSubscription(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Security $security,
        WorkflowInterface $cancellationRequestStateMachine,
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

        $user = $security->getUser();

        if (!$user instanceof BillingAdminInterface) {
            throw new \LogicException('User is not a billing admin');
        }

        $cancellationRequest = new CancellationRequest();
        $cancellationRequest->setSubscription($subscription);
        $cancellationRequest->setBillingAdmin($user);
        $cancellationRequest->setCreatedAt(new \DateTime());
        $cancellationRequest->setWhen($dto->getWhen());
        $cancellationRequest->setSpecificDate($dto->getComment());
        $cancellationRequest->setRefundType($dto->getRefundType());
        $cancellationRequest->setComment($dto->getComment());
        $cancellationRequest->setState('started');

        $cancellationRequestRepository->save($cancellationRequest);

        try {
            $cancellationRequestStateMachine->apply($cancellationRequest, 'cancel_subscription');
            $cancellationRequestStateMachine->apply($cancellationRequest, 'issue_refund');
            $cancellationRequestStateMachine->apply($cancellationRequest, 'send_customer_notice');
            $cancellationRequestStateMachine->apply($cancellationRequest, 'send_internal_notice');
        } catch (\Throwable $exception) {
            $cancellationRequestRepository->save($cancellationRequest);
            throw $exception;

            return new JsonResponse(['error' => $exception->getMessage(), 'class' => get_class($exception)], status: JsonResponse::HTTP_FAILED_DEPENDENCY);
        }

        $cancellationRequestRepository->save($cancellationRequest);

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }
}
