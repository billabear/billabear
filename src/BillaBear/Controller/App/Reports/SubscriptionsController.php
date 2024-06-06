<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Reports;

use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SubscriptionsController
{
    use LoggerAwareTrait;

    #[Route('/app/reports/subscriptions', name: 'app_app_reports_subscriptions_getoverview', methods: ['GET'])]
    public function getOverview(
        SubscriptionRepositoryInterface $subscriptionRepository,
    ): Response {
        $this->getLogger()->info('Received a request to view subscription overview report');

        $data = [];
        $data['subscriptions'] = $subscriptionRepository->getPlanCounts();
        $data['schedule'] = $subscriptionRepository->getScheduleCounts();

        return new JsonResponse($data);
    }

    #[Route('/app/reports/subscriptions/churn', name: 'app_app_reports_subscriptions_getchurnreport', methods: ['GET'])]
    public function getChurnReport(
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
    ): Response {
        $this->getLogger()->info('Received a request to view subscription churn report');

        $data = [];
        $data['daily'] = $cancellationRequestRepository->getDailyCount(new \DateTime('-35 days'));
        $data['monthly'] = $cancellationRequestRepository->getMonthlyCount(new \DateTime('-14 months'));
        $data['yearly'] = $cancellationRequestRepository->getYearlyCount(new \DateTime('-6 years'));

        return new JsonResponse($data);
    }
}
