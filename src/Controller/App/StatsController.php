<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\Dto\Response\App\Stats\MainDashboardHeader;
use App\Dto\Response\App\Stats\MainDashboardStats;
use App\Entity\Stats\CachedStats;
use App\Invoice\UnpaidInvoiceStatsProvider;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\Stats\CachedStatsRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use App\Stats\Graphs\ChargeBackAmountStatsProvider;
use App\Stats\Graphs\PaymentAmountStatsProvider;
use App\Stats\Graphs\RefundAmountStatsProvider;
use App\Stats\Graphs\SubscriptionCancellationStatsProvider;
use App\Stats\Graphs\SubscriptionCountStatsProvider;
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
        SubscriptionCountStatsProvider $subscriptionCountStatsProvider,
        SubscriptionCreationStatsProvider $subscriptionCreationStatsProvider,
        SubscriptionCancellationStatsProvider $subscriptionCancellationStatsProvider,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        CachedStatsRepositoryInterface $cachedStatsRepository,
        SettingsRepositoryInterface $settingsRepository,
        UnpaidInvoiceStatsProvider $invoiceStatsProvider,
    ): Response {
        $headerStats = new MainDashboardHeader();
        $headerStats->setActiveSubscriptions($subscriptionRepository->getCountActive());
        $headerStats->setActiveCustomers($subscriptionRepository->getCountOfActiveCustomers());
        list($count, $amount) = $invoiceStatsProvider->getStats();
        $headerStats->setUnpaidInvoicesCount($count);
        $headerStats->setUnpaidInvoicesAmount($amount);

        $mainDashboardStat = new MainDashboardStats();
        $mainDashboardStat->setHeader($headerStats);
        $mainDashboardStat->setPaymentAmount($paymentAmountStatsProvider->getMainDashboard());
        $mainDashboardStat->setRefundAmount($refundAmountStatsProvider->getMainDashboard());
        $mainDashboardStat->setChargeBackAmount($chargeBackAmountStatsProvider->getMainDashboard());
        $mainDashboardStat->setSubscriptionCount($subscriptionCountStatsProvider->getMainDashboard());
        $mainDashboardStat->setSubscriptionCreation($subscriptionCreationStatsProvider->getMainDashboard());
        $mainDashboardStat->setSubscriptionCancellation($subscriptionCancellationStatsProvider->getMainDashboard());
        $mainDashboardStat->setCurrency($settingsRepository->getDefaultSettings()->getSystemSettings()->getMainCurrency());

        $mrrCache = $cachedStatsRepository->getMoneyStat(CachedStats::STAT_NAME_ESTIMATED_MRR);
        $arrCache = $cachedStatsRepository->getMoneyStat(CachedStats::STAT_NAME_ESTIMATED_ARR);

        $mainDashboardStat->setEstimatedAtt($arrCache->getValue());
        $mainDashboardStat->setEstimatedMrr($mrrCache->getValue());

        $json = $serializer->serialize($mainDashboardStat, 'json');

        return new JsonResponse($json, json: true);
    }
}
