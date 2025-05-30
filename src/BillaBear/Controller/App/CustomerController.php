<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Customer\CreationHandler;
use BillaBear\Customer\CustomerStatus;
use BillaBear\Customer\Disabler;
use BillaBear\Customer\ExternalRegisterInterface;
use BillaBear\Customer\LimitsFactory;
use BillaBear\Customer\Messenger\CustomerEvent;
use BillaBear\Customer\Messenger\CustomerEventType;
use BillaBear\Customer\ObolRegister;
use BillaBear\DataMappers\CreditDataMapper;
use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\Invoice\InvoiceDeliverySettingsDataMapper;
use BillaBear\DataMappers\InvoiceDataMapper;
use BillaBear\DataMappers\PaymentDataMapper;
use BillaBear\DataMappers\PaymentMethodsDataMapper;
use BillaBear\DataMappers\RefundDataMapper;
use BillaBear\DataMappers\Settings\BrandSettingsDataMapper;
use BillaBear\DataMappers\Subscriptions\CustomerSubscriptionEventDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\DataMappers\Usage\MetricCounterDataMapper;
use BillaBear\DataMappers\Usage\UsageLimitDataMapper;
use BillaBear\Dto\Generic\App\Usage\MetricCounter;
use BillaBear\Dto\Request\App\CreateCustomerDto;
use BillaBear\Dto\Response\App\Customer\CreateCustomerView;
use BillaBear\Dto\Response\App\CustomerView;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Event\Customer\CustomerEnabled;
use BillaBear\Filters\CustomerList;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\CreditRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\CustomerSubscriptionEventRepositoryInterface;
use BillaBear\Repository\InvoiceDeliverySettingsRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use BillaBear\Repository\PaymentRepositoryInterface;
use BillaBear\Repository\RefundRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Repository\UsageLimitRepositoryInterface;
use BillaBear\Stats\CustomerCreationStats;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerEnabledPayload;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerUpdatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/customer', name: 'site_customer_list', methods: ['GET'])]
    public function listCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        CustomerDataMapper $customerFactory,
    ): Response {
        $this->getLogger()->info('Received request to list customers');

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

        $filterBuilder = new CustomerList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $customerRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$customerFactory, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/customer/create', name: 'app_customer_create_view', methods: ['GET'])]
    public function createCustomerView(
        Request $request,
        BrandSettingsRepositoryInterface $settingsRepository,
        BrandSettingsDataMapper $brandSettingsFactory,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to read create customer');

        $brandSettings = $settingsRepository->getAll();
        $brandDtos = array_map([$brandSettingsFactory, 'createAppDto'], $brandSettings);

        $viewDto = new CreateCustomerView();
        $viewDto->setBrands($brandDtos);

        $json = $serializer->serialize($viewDto, 'json');

        return JsonResponse::fromJsonString($json);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/customer', name: 'app_customer_create', methods: ['POST'])]
    public function createCustomer(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerDataMapper $customerFactory,
        ExternalRegisterInterface $externalRegister,
        CustomerRepositoryInterface $customerRepository,
        CustomerCreationStats $customerCreationStats,
        CreationHandler $creationHandler,
    ): Response {
        $this->getLogger()->info('Received request to write create customer');

        $dto = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
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

        $customer = $customerFactory->createCustomer($dto);

        if ($customerRepository->hasCustomerByEmail((string) $customer->getBillingEmail())) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_CONFLICT);
        }

        $creationHandler->handleCreation($customer);

        $dto = $customerFactory->createAppDto($customer);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{id}/disable', name: 'app_customer_disable', methods: ['POST'])]
    public function disableCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        Disabler $disabler,
    ) {
        $this->getLogger()->info('Received request to disable customer', ['customer_id' => $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $disabler->disable($customer);

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{id}/enable', name: 'app_customer_enable', methods: ['POST'])]
    public function enableCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        WebhookDispatcherInterface $eventProcessor,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->getLogger()->info('Received request to enable customer', ['customer_id' => $request->get('id')]);

        $this->getLogger()->info('Starting customer enable APP request');

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            $this->getLogger()->info('Unable to find customer to enable via APP', ['id' => (string) $request->get('id')]);

            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $customer->setStatus(CustomerStatus::ACTIVE);
        $customerRepository->save($customer);
        $eventProcessor->dispatch(new CustomerEnabledPayload($customer));
        $eventDispatcher->dispatch(new CustomerEnabled($customer), CustomerEnabled::NAME);

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/customer/{id}', name: 'app_customer_view', methods: ['GET'])]
    public function viewCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        CustomerDataMapper $customerDataMapper,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PaymentMethodsDataMapper $paymentDetailsFactory,
        PaymentRepositoryInterface $paymentRepository,
        PaymentDataMapper $paymentDataMapper,
        RefundRepositoryInterface $refundRepository,
        RefundDataMapper $refundDataMapper,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionFactory,
        LimitsFactory $limitsFactory,
        CreditRepositoryInterface $creditNoteRepository,
        CreditDataMapper $creditDataMapper,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataMapper $invoiceDataMapper,
        CustomerSubscriptionEventRepositoryInterface $customerSubscriptionEventRepository,
        CustomerSubscriptionEventDataMapper $customerSubscriptionEventDataMapper,
        InvoiceDeliverySettingsRepositoryInterface $invoiceDeliveryRepository,
        InvoiceDeliverySettingsDataMapper $invoiceDeliveryDataMapper,
        MetricCounterDataMapper $metricCounterDataMapper,
        UsageLimitRepositoryInterface $usageLimitRepository,
        UsageLimitDataMapper $usageLimitDataMapper,
    ): Response {
        $this->getLogger()->info('Received request to view customer', ['customer_id' => $request->get('id')]);

        try {
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $payments = $paymentRepository->getLastTenForCustomer($customer);
        $paymentDtos = array_map([$paymentDataMapper, 'createAppDto'], $payments->getResults());

        $paymentList = new ListResponse();
        $paymentList->setData($paymentDtos);
        $paymentList->setLastKey($payments->getLastKey());
        $paymentList->setFirstKey($payments->getFirstKey());
        $paymentList->setHasMore($payments->hasMore());

        $refunds = $refundRepository->getLastTenForCustomer($customer);
        $refundDtos = array_map([$refundDataMapper, 'createAppDto'], $refunds->getResults());

        $refundList = new ListResponse();
        $refundList->setData($refundDtos);
        $refundList->setHasMore($refunds->hasMore());
        $refundList->setFirstKey($refundList->getFirstKey());
        $refundList->setLastKey($refundList->getLastKey());

        $paymentDetails = $paymentDetailsRepository->getPaymentCardForCustomer($customer);
        $paymentDetailsDto = array_map([$paymentDetailsFactory, 'createAppDto'], $paymentDetails);

        $subscriptions = $subscriptionRepository->getLastTenForCustomer($customer);
        $subscriptionDtos = array_map([$subscriptionFactory, 'createAppDto'], $subscriptions->getResults());

        /** @var MetricCounter[] $metricCounterDtos */
        $metricCounterDtos = [];
        /** @var Subscription $subscription */
        foreach ($subscriptions->getResults() as $subscription) {
            if ($subscription->isActive() && $subscription->getPrice()?->getUsage()) {
                $metric = $metricCounterDataMapper->createAppDto($subscription);
                $found = false;
                foreach ($metricCounterDtos as $dto) {
                    if ($metric->id === $dto->id) {
                        $found = true;
                    }
                }
                if ($found) {
                    continue;
                }
                $metricCounterDtos[] = $metric;
            }
        }

        $subscriptionList = new ListResponse();
        $subscriptionList->setData($subscriptionDtos);
        $subscriptionList->setHasMore($subscriptions->hasMore());
        $subscriptionList->setLastKey($subscriptions->getLastKey());
        $subscriptionList->setFirstKey($subscriptions->getFirstKey());

        $allSubscriptions = $subscriptionRepository->getAllForCustomer($customer);

        $limits = $limitsFactory->createAppDto($customer, $allSubscriptions);

        $creditNotes = $creditNoteRepository->getForCustomer($customer);
        $creditNotesDto = array_map([$creditDataMapper, 'createAppDto'], $creditNotes);

        $invoices = $invoiceRepository->getLastTenForCustomer($customer);
        $invoiceDtos = array_map([$invoiceDataMapper, 'createQuickViewAppDto'], $invoices->getResults());

        $invoiceList = new ListResponse();
        $invoiceList->setData($invoiceDtos);
        $invoiceList->setHasMore($invoices->hasMore());
        $invoiceList->setLastKey($invoices->getLastKey());
        $invoiceList->setFirstKey($invoices->getFirstKey());

        $subscriptionEvents = $customerSubscriptionEventRepository->getLastTenForCustomer($customer);
        $subscriptionEventDtos = array_map([$customerSubscriptionEventDataMapper, 'createAppDto'], $subscriptionEvents);

        $invoiceDelivery = $invoiceDeliveryRepository->getAllForCustomer($customer);
        $invoiceDeliveryDtos = array_map([$invoiceDeliveryDataMapper, 'createAppDto'], $invoiceDelivery);

        $invoiceDeliveryList = new ListResponse();
        $invoiceDeliveryList->setData($invoiceDeliveryDtos);
        $invoiceDeliveryList->setHasMore(false);

        $usageLimits = $usageLimitRepository->getForCustomer($customer);
        $usageLimitsDto = array_map([$usageLimitDataMapper, 'createAppDto'], $usageLimits);

        $customerDto = $customerDataMapper->createAppDto($customer);
        $dto = new CustomerView();
        $dto->setCustomer($customerDto);
        $dto->setPaymentDetails($paymentDetailsDto);
        $dto->setSubscriptions($subscriptionList);
        $dto->setPayments($paymentList);
        $dto->setRefunds($refundList);
        $dto->setLimits($limits);
        $dto->setCredit($creditNotesDto);
        $dto->setInvoices($invoiceList);
        $dto->setInvoiceDelivery($invoiceDeliveryList);
        $dto->setSubscriptionEvents($subscriptionEventDtos);
        $dto->setMetricCounters($metricCounterDtos);
        $dto->setUsageLimits($usageLimitsDto);
        $output = $serializer->serialize($dto, 'json');

        return new JsonResponse($output, json: true);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{id}', name: 'app_customer_update', methods: ['POST'])]
    public function updateCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerDataMapper $customerFactory,
        ObolRegister $obolRegister,
        WebhookDispatcherInterface $webhookDispatcher,
        MessageBusInterface $messageBus,
    ): Response {
        $this->getLogger()->info('Received request to update customer', ['customer_id' => $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var CreateCustomerDto $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
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

        $newCustomer = $customerFactory->createCustomer($dto, $customer);
        $obolRegister->update($newCustomer);

        $customerRepository->save($newCustomer);

        $webhookDispatcher->dispatch(new CustomerUpdatedPayload($customer));
        $messageBus->dispatch(new CustomerEvent(CustomerEventType::UPDATE, (string) $customer->getId()));

        $dto = $customerFactory->createAppDto($newCustomer);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_ACCEPTED, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
