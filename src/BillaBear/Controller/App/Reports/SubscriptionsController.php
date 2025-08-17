<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Reports;

use BillaBear\Dto\Response\App\Stats\MonthlySubscriptionStats;
use BillaBear\Dto\Response\App\Stats\SubscriptionMovementStats;
use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Repository\Stats\SubscriptionMovementStatsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SubscriptionsController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

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

    #[Route('/app/reports/subscriptions/new', name: 'app_app_reports_subscriptions_getnewstats', methods: ['GET'])]
    public function getNewSubscriptionStats(
        SubscriptionMovementStatsRepositoryInterface $subscriptionMovementStatsRepository,
    ): Response {
        $this->getLogger()->info('Received a request to view new subscription stats');

        $months = [];
        $now = new \DateTime();

        // Get data for the last 12 months
        for ($i = 0; $i < 12; ++$i) {
            $monthDate = clone $now;
            $monthDate->modify("-{$i} month");
            $monthDate->setTime(0, 0, 0);
            $monthDate->modify('first day of this month');

            $monthName = $monthDate->format('F Y');

            $months[] = new MonthlySubscriptionStats(
                month: $monthName,
                existing: $subscriptionMovementStatsRepository->getExistingSubscriptionsCountForMonth($monthDate),
                new: $subscriptionMovementStatsRepository->getNewSubscriptionsCountForMonth($monthDate),
                upgrades: $subscriptionMovementStatsRepository->getUpgradesCountForMonth($monthDate),
                downgrades: $subscriptionMovementStatsRepository->getDowngradesCountForMonth($monthDate),
                cancellations: $subscriptionMovementStatsRepository->getCancellationsCountForMonth($monthDate),
                reactivations: $subscriptionMovementStatsRepository->getReactivationsCountForMonth($monthDate)
            );
        }

        // Reverse the array to have months in chronological order
        $months = array_reverse($months);

        $stats = new SubscriptionMovementStats($months);

        return new JsonResponse($stats);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
