<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Reports;

use App\Repository\SubscriptionRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionsController
{
    #[Route('/app/reports/subscriptions', name: 'app_app_reports_subscriptions_getoverview', methods: ['GET'])]
    public function getOverview(
        SubscriptionRepositoryInterface $subscriptionRepository,
    ): Response {
        $data = [];
        $data['subscriptions'] = $subscriptionRepository->getPlanCounts();
        $data['schedule'] = $subscriptionRepository->getScheduleCounts();

        return new JsonResponse($data);
    }
}
