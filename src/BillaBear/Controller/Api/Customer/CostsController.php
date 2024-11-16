<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api\Customer;

use BillaBear\Dto\Response\Api\Customer\Cost;
use BillaBear\Dto\Response\Api\Customer\Costs;
use BillaBear\Dto\Response\Api\Customer\MetricCost;
use BillaBear\Exception\Invoice\CannotEstimateException;
use BillaBear\Invoice\Usage\CostEstimator;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Money;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CostsController
{
    use LoggerAwareTrait;

    #[Route('/api/v1/customer/{id}/costs', name: 'api_v1_customer_read_costs', methods: ['GET'])]
    public function costAction(
        Request $request,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        CostEstimator $costEstimator,
    ): Response {
        $this->getLogger()->info('Received an API request to read the customer costs', ['customer_id' => $request->get('id')]);
        try {
            $customer = $customerRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->warning('Received an API request to read customer costs for a non-existent customer', ['customer_id' => $request->get('id')]);

            return new Response(null, 404);
        }

        $costs = [];
        $money = null;
        $subscriptions = $subscriptionRepository->getAllActiveForCustomer($customer);
        foreach ($subscriptions as $subscription) {
            if (!$subscription->getPrice()?->getUsage()) {
                continue;
            }
            try {
                $estimate = $costEstimator->getEstimate($subscription);
            } catch (CannotEstimateException) {
                $this->getLogger()->info('Cannot estimate cost for subscription', ['subscription_id' => (string) $subscription->getId()]);

                return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!$money) {
                $money = Money::zero($subscription->getCurrency());
            }

            try {
                $money = $money->plus($estimate->cost);
            } catch (MoneyMismatchException $e) {
                $this->getLogger()->critical('Have a currency mismatch when creating estimate cost', ['exception_message' => $e->getMessage()]);

                return new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $costDto = new MetricCost();
            $costDto->setAmount($estimate->cost->getMinorAmount()->toInt());
            $costDto->setCurrency($estimate->cost->getCurrency());
            $costDto->setUsage($estimate->usage);
            $costDto->setName($estimate->metricName);
            $costs[] = $costDto;
        }

        $totalCost = new Cost();
        $totalCost->setAmount($money?->getMinorAmount()->toInt() ?? 0);
        $totalCost->setCurrency($money?->getCurrency()->getCurrencyCode() ?? '');
        $cost = new Costs();
        $cost->setTotal($totalCost);
        $cost->setCosts($costs);

        $json = $serializer->serialize($cost, 'json');

        return new JsonResponse($json, Response::HTTP_OK, json: true);
    }
}
