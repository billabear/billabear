<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\PaymentDataMapper;
use BillaBear\DataMappers\Subscriptions\CustomerSubscriptionEventDataMapper;
use BillaBear\Dto\Response\App\Stats\MainDashboardHeader;
use BillaBear\Dto\Response\App\Stats\MainDashboardStats;
use BillaBear\Entity\Stats\CachedStats;
use BillaBear\Invoice\UnpaidInvoiceStatsProvider;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\CustomerSubscriptionEventRepositoryInterface;
use BillaBear\Repository\PaymentRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\Stats\Aggregate\CachedStatsRepositoryInterface;
use BillaBear\Repository\Stats\PaymentStatsRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Stats\Graphs\RevenueStatsProvider;
use BillaBear\Stats\Graphs\SubscriptionCountStatsProvider;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DashboardController
{
    use LoggerAwareTrait;

    #[Route('/app/stats', name: 'app_app_stats_returnstats', methods: ['GET'])]
    public function returnStats(
        SubscriptionCountStatsProvider $subscriptionCountStatsProvider,
        PaymentStatsRepositoryInterface $paymentStatsRepository,
        RevenueStatsProvider $revenueStatsProvider,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SerializerInterface $serializer,
        CachedStatsRepositoryInterface $cachedStatsRepository,
        SettingsRepositoryInterface $settingsRepository,
        UnpaidInvoiceStatsProvider $invoiceStatsProvider,
        CustomerRepositoryInterface $customerRepository,
        CustomerDataMapper $customerDataMapper,
        CustomerSubscriptionEventRepositoryInterface $customerSubscriptionEventRepository,
        CustomerSubscriptionEventDataMapper $subscriptionEventDataMapper,
        PaymentRepositoryInterface $paymentRepository,
        PaymentDataMapper $paymentDataMapper,
        CacheInterface $appCacheShort,
    ): Response {
        $this->getLogger()->info('Received request for dashboard stats');

        $mainDashboardStat = $appCacheShort->get('dashboard_stats', function (ItemInterface $item) use (
            $subscriptionRepository,
            $invoiceStatsProvider,
            $subscriptionCountStatsProvider,
            $settingsRepository,
            $cachedStatsRepository,
            $customerRepository,
            $customerDataMapper,
            $customerSubscriptionEventRepository,
            $subscriptionEventDataMapper,
            $paymentRepository,
            $paymentDataMapper,
            $revenueStatsProvider,
        ) {
            $item->expiresAfter(\DateInterval::createFromDateString('5 minutes'));
            $headerStats = new MainDashboardHeader();
            $headerStats->setActiveSubscriptions($subscriptionRepository->getCountActive());
            $headerStats->setActiveCustomers($subscriptionRepository->getCountOfActiveCustomers());
            list($count, $amount) = $invoiceStatsProvider->getStats();
            $headerStats->setUnpaidInvoicesCount($count);
            $headerStats->setUnpaidInvoicesAmount($amount);

            $mainDashboardStat = new MainDashboardStats();
            $mainDashboardStat->setHeader($headerStats);
            $mainDashboardStat->setRevenueStats($revenueStatsProvider->getMainDashboard());
            $mainDashboardStat->setSubscriptionCount($subscriptionCountStatsProvider->getMainDashboard());
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

            $payments = $paymentRepository->getLatest();
            $paymentDtos = array_map([$paymentDataMapper, 'createAppDto'], $payments);
            $mainDashboardStat->setLatestPayments($paymentDtos);

            return $mainDashboardStat;
        });

        $json = $serializer->serialize($mainDashboardStat, 'json');

        return new JsonResponse($json, json: true);
    }
}
