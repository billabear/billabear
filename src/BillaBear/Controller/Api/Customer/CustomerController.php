<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api\Customer;

use BillaBear\Customer\CreationHandler;
use BillaBear\Customer\CustomerStatus;
use BillaBear\Customer\LimitsFactory;
use BillaBear\Customer\Messenger\CustomerEvent;
use BillaBear\Customer\Messenger\CustomerEventType;
use BillaBear\Customer\PortalManageLinkGeneratorInterface;
use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\Dto\Request\Api\CreateCustomerDto;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Entity\Customer;
use BillaBear\Entity\ManageCustomerSession;
use BillaBear\Filters\CustomerList;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\ManageCustomerSessionRepositoryInterface;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerCreatedPayload;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerDisabledPayload;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerEnabledPayload;
use BillaBear\Webhook\Outbound\Payload\Customer\CustomerUpdatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Obol\Exception\ProviderFailureException;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/customer', name: 'api_v1_customer_create', methods: ['POST'])]
    public function createCustomer(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerDataMapper $customerFactory,
        CreationHandler $creationHandler,
        WebhookDispatcherInterface $webhookDispatcher,
    ): Response {
        $this->getLogger()->info('Start create customer API request', ['customer_id' => (string) $request->get('id')]);

        $dto = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }
            $this->getLogger()->info('Customer creation validation errors', ['errors' => $errorOutput]);

            return new JsonResponse([
                'errors' => $errorOutput,
            ], Response::HTTP_BAD_REQUEST);
        }

        $customer = $customerFactory->createCustomer($dto);

        try {
            $creationHandler->handleCreation($customer);
        } catch (ProviderFailureException $e) {
            $this->getLogger()->error('Got an error from payment provider', ['exception_message' => $e->getPrevious()->getMessage()]);

            return new JsonResponse([], Response::HTTP_FAILED_DEPENDENCY);
        }
        $this->getLogger()->info('Customer creation complete');

        $dto = $customerFactory->createApiDto($customer);
        $jsonResponse = $serializer->serialize($dto, 'json');
        $webhookDispatcher->dispatch(new CustomerCreatedPayload($customer));

        return new JsonResponse($jsonResponse, Response::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/customer', name: 'api_v1_customer_list', methods: ['GET'])]
    public function listCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        CustomerDataMapper $customerFactory,
    ): Response {
        $this->getLogger()->info('Started list customer API request', ['customer_id' => (string) $request->get('id')]);

        $lastKey = $request->get('last_key');
        $resultsPerPage = (int) $request->get('limit', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'reason' => 'limit is below 1',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'reason' => 'limit is above 100',
            ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $filterBuilder = new CustomerList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $customerRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
        );

        $dtos = array_map([$customerFactory, 'createApiDto'], $resultSet->getResults());

        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/customer/{id}', name: 'api_v1_customer_read', methods: ['GET'])]
    public function readCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        CustomerDataMapper $customerFactory,
    ): Response {
        $this->getLogger()->info('Starting read customer API request', ['customer_id' => (string) $request->get('id')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->info('Unable to find customer for read request', ['customer_id' => (string) $request->get('id')]);

            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
        $dto = $customerFactory->createApiDto($customer);
        $data = $serializer->serialize($dto, 'json');

        return new JsonResponse($data, json: true);
    }

    #[Route('/api/v1/customer/{id}/limits', name: 'api_v1_customer_read_limits', methods: ['GET'])]
    public function readCustomerLimits(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        LimitsFactory $factory,
    ): Response {
        $this->getLogger()->info('Starting customer limits API request', ['customer_id' => (string) $request->get('id')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->info('Unable to find customer to provide limits for', ['id' => (string) $request->get('id')]);

            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $subscriptions = $subscriptionRepository->getAllActiveForCustomer($customer);
        $dto = $factory->createApiDto($customer, $subscriptions);
        $data = $serializer->serialize($dto, 'json');

        return new JsonResponse($data, json: true);
    }

    #[Route('/api/v1/customer/{id}', name: 'api_v1_customer_update', methods: ['PUT'])]
    public function updateCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerDataMapper $customerFactory,
        WebhookDispatcherInterface $webhookDispatcher,
        MessageBusInterface $messageBus,
    ): Response {
        $this->getLogger()->info('Starting customer update API request', ['customer_id' => (string) $request->get('id')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->info('Unable to find customer to update via API', ['id' => (string) $request->get('id')]);

            return new JsonResponse([], Response::HTTP_NOT_FOUND);
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
            ], Response::HTTP_BAD_REQUEST);
        }

        $newCustomer = $customerFactory->createCustomer($dto, $customer);

        $customerRepository->save($newCustomer);
        $dto = $customerFactory->createApiDto($newCustomer);
        $jsonResponse = $serializer->serialize($dto, 'json');
        $webhookDispatcher->dispatch(new CustomerUpdatedPayload($customer));

        $messageBus->dispatch(new CustomerEvent(CustomerEventType::UPDATE, (string) $customer->getId()));

        return new JsonResponse($jsonResponse, Response::HTTP_ACCEPTED, json: true);
    }

    #[Route('/api/v1/customer/{id}/disable', name: 'api_v1_customer_disable', methods: ['POST'])]
    public function disableCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        WebhookDispatcherInterface $webhookDispatcher,
        MessageBusInterface $messageBus,
    ): JsonResponse {
        $this->getLogger()->info('Starting customer disable API request', ['customer_id' => (string) $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->info('Unable to find customer to disable', ['id' => (string) $request->get('id')]);

            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $customer->setStatus(CustomerStatus::DISABLED);
        $customerRepository->save($customer);
        $webhookDispatcher->dispatch(new CustomerDisabledPayload($customer));
        $messageBus->dispatch(new CustomerEvent(CustomerEventType::UPDATE, (string) $customer->getId()));

        return new JsonResponse(status: Response::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/customer/{id}/enable', name: 'api_v1_customer_enable', methods: ['POST'])]
    public function enableCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        WebhookDispatcherInterface $webhookDispatcher,
        MessageBusInterface $messageBus,
    ): JsonResponse {
        $this->getLogger()->info('Starting customer enable API request', ['customer_id' => (string) $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->info('Unable to find customer to enable', ['id' => (string) $request->get('id')]);

            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $customer->setStatus(CustomerStatus::ACTIVE);
        $customerRepository->save($customer);
        $webhookDispatcher->dispatch(new CustomerEnabledPayload($customer));
        $messageBus->dispatch(new CustomerEvent(CustomerEventType::UPDATE, (string) $customer->getId()));

        return new JsonResponse(status: Response::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/customer/{id}/manage-link', name: 'api_v1_customer_manage_link', methods: ['GET'])]
    public function manageCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        ManageCustomerSessionRepositoryInterface $manageCustomerSessionRepository,
        SerializerInterface $serializer,
        PortalManageLinkGeneratorInterface $portalManageLinkGenerator,
    ) {
        $this->getLogger()->info('Fetching a customer session manage link token', ['customer_id' => (string) $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->info('Unable to find customer for manage customer session', ['id' => (string) $request->get('id')]);

            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $token = bin2hex(random_bytes(32));

        $manage = new ManageCustomerSession();
        $manage->setToken($token);
        $manage->setCreatedAt(new \DateTime());
        $manage->setUpdatedAt(new \DateTime());
        $manage->setExpiresAt(new \DateTime('+5 minutes'));
        $manage->setCustomer($customer);

        $manageCustomerSessionRepository->save($manage);
        $url = $portalManageLinkGenerator->generate($manage);

        $dto = new \BillaBear\Dto\Generic\Api\ManageCustomerSession($token, $url);
        $json = $serializer->serialize($dto, 'json');

        return JsonResponse::fromJsonString($json);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
