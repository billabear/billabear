<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\Subscriptions\CustomerSubscriptionEventDataMapper;
use BillaBear\Dto\Response\App\Stats\MainDashboardHeader;
use BillaBear\Dto\Response\App\Stats\MainDashboardStats;
use BillaBear\Entity\Stats\CachedStats;
use BillaBear\Invoice\UnpaidInvoiceStatsProvider;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\CustomerSubscriptionEventRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\Stats\CachedStatsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Stats\Graphs\ChargeBackAmountStatsProvider;
use BillaBear\Stats\Graphs\PaymentAmountStatsProvider;
use BillaBear\Stats\Graphs\RefundAmountStatsProvider;
use BillaBear\Stats\Graphs\SubscriptionCancellationStatsProvider;
use BillaBear\Stats\Graphs\SubscriptionCountStatsProvider;
use BillaBear\Stats\Graphs\SubscriptionCreationStatsProvider;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DashboardController
{
    use LoggerAwareTrait;

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
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerDataMapper,
        CustomerSubscriptionEventRepositoryInterface $customerSubscriptionEventRepository,
        CustomerSubscriptionEventDataMapper $subscriptionEventDataMapper,
    ): Response {
        $this->getLogger()->info('Received request for dashboard stats');

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

        $customers = $customerRepository->getLatestCustomers();
        $customerDtos = array_map([$customerDataMapper, 'createAppDto'], $customers);
        $mainDashboardStat->setLatestCustomers($customerDtos);

        $subscriptionEvents = $customerSubscriptionEventRepository->getLatest();
        $eventDtos = array_map([$subscriptionEventDataMapper, 'createAppDto'], $subscriptionEvents);
        $mainDashboardStat->setSubscriptionEvents($eventDtos);

        $json = $serializer->serialize($mainDashboardStat, 'json');

        return new JsonResponse($json, json: true);
    }
}
