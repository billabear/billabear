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

use App\Dto\Response\App\Stats\MainDashboardStats;
use App\Stats\Graphs\ChargeBackAmountStatsProvider;
use App\Stats\Graphs\PaymentAmountStatsProvider;
use App\Stats\Graphs\RefundAmountStatsProvider;
use App\Stats\Graphs\SubscriptionCancellationStatsProvider;
use App\Stats\Graphs\SubscriptionCreationStatsProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class StatsController
{
    #[Route('/app/stats', name: 'app_app_stats_returnstats', methods: ['GET'])]
    public function returnStats(
        PaymentAmountStatsProvider $paymentAmountStatsProvider,
        ChargeBackAmountStatsProvider $chargeBackAmountStatsProvider,
        RefundAmountStatsProvider $refundAmountStatsProvider,
        SubscriptionCreationStatsProvider $subscriptionCreationStatsProvider,
        SubscriptionCancellationStatsProvider $subscriptionCancellationStatsProvider,
        SerializerInterface $serializer,
    ): Response {
        $mainDashboardStat = new MainDashboardStats();
        $mainDashboardStat->setPaymentAmount($paymentAmountStatsProvider->getMainDashboard());
        $mainDashboardStat->setRefundAmount($refundAmountStatsProvider->getMainDashboard());
        $mainDashboardStat->setChargeBackAmount($chargeBackAmountStatsProvider->getMainDashboard());
        $mainDashboardStat->setSubscriptionCreation($subscriptionCreationStatsProvider->getMainDashboard());
        $mainDashboardStat->setSubscriptionCancellation($subscriptionCancellationStatsProvider->getMainDashboard());

        $json = $serializer->serialize($mainDashboardStat, 'json');

        return new JsonResponse($json, json: true);
    }
}
