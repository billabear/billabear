<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use BillaBear\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SubscriptionPlanController
{
    use CrudListTrait;
    use LoggerAwareTrait;

    #[Route('/api/v1/subscription/plans', name: 'api_v1_subscription_plan_list', methods: ['GET'], priority: 99)]
    public function listSubscriptionPlan(
        Request $request,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SerializerInterface $serializer,
        SubscriptionPlanDataMapper $subscriptionPlanDataMapper,
    ): Response {
        $this->getLogger()->info('Received request to view all subscriptions');

        return $this->crudList($request, $subscriptionPlanRepository, $serializer, $subscriptionPlanDataMapper, 'id');
    }
}
