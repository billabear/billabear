<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Compliance;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionDataMapper;
use BillaBear\Dto\Response\App\Compliance\CustomerList;
use BillaBear\Dto\Response\App\Compliance\SubscriptionList;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Subscription;
use BillaBear\Repository\Audit\AuditLogRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class AuditLogController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/audit', name: 'app_compliance_audit', methods: ['GET'])]
    public function listAction(
        Request $request,
        AuditLogRepositoryInterface $auditLogRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Listing audit logs');

        $key = $request->get('last_key', null);
        $reverse = false;
        $firstKey = $request->get('first_key', null);
        if ($firstKey) {
            $key = $firstKey;
            $reverse = true;
        }

        $result = $auditLogRepository->findAll($key, $request->get('limit', 25), $reverse);

        $crudView = new ListResponse();
        $crudView->setData($result->getResults());
        $crudView->setLastKey($result->getLastKey());
        $crudView->setFirstKey($result->getFirstKey());
        $crudView->setHasMore($result->hasMore());

        $json = $serializer->serialize($crudView, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/app/audit/customer/{id}', name: 'app_compliance_audit_customer', methods: ['GET'])]
    public function customerListAction(
        Request $request,
        AuditLogRepositoryInterface $auditLogRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received a request to view customer audit logs', ['customer_id' => $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $key = $request->get('last_key', null);
        $reverse = false;
        $firstKey = $request->get('first_key', null);
        if ($firstKey) {
            $key = $firstKey;
            $reverse = true;
        }

        $result = $auditLogRepository->findAllForAuditableEntity($customer, $key, $request->get('limit', 25), $reverse);

        $crudView = new ListResponse();
        $crudView->setData($result->getResults());
        $crudView->setLastKey($result->getLastKey());
        $crudView->setFirstKey($result->getFirstKey());
        $crudView->setHasMore($result->hasMore());

        $view = new CustomerList(
            $customerDataMapper->createAppDto($customer),
            $crudView,
        );

        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/app/audit/subscription/{id}', name: 'app_compliance_audit_subscription', methods: ['GET'])]
    public function subscriptionListAction(
        Request $request,
        AuditLogRepositoryInterface $auditLogRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received a request to view subscription audit logs', ['subscription_id' => $request->get('id')]);

        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $key = $request->get('last_key', null);
        $reverse = false;
        $firstKey = $request->get('first_key', null);
        if ($firstKey) {
            $key = $firstKey;
            $reverse = true;
        }

        $result = $auditLogRepository->findAllForAuditableEntity($subscription, $key, $request->get('limit', 25), $reverse);

        $crudView = new ListResponse();
        $crudView->setData($result->getResults());
        $crudView->setLastKey($result->getLastKey());
        $crudView->setFirstKey($result->getFirstKey());
        $crudView->setHasMore($result->hasMore());

        $view = new SubscriptionList(
            $subscriptionDataMapper->createAppDto($subscription),
            $crudView,
        );

        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
