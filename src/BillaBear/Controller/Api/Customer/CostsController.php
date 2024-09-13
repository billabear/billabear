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
use BillaBear\Invoice\Usage\CostEstimator;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
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
        } catch (NoEntityFoundException $exception) {
            $this->getLogger()->warning('Received an API request to read customer costs for a non-existant customer', ['customer_id' => $request->get('id')]);

            return new Response(null, 404);
        }

        $costs = [];
        $money = null;
        $subscriptions = $subscriptionRepository->getAllActiveForCustomer($customer);
        foreach ($subscriptions as $subscription) {
            if (!$subscription->getPrice()?->getUsage()) {
                continue;
            }
            $estimate = $costEstimator->getEstimate($subscription);
            if (!$money) {
                $money = Money::zero($subscription->getCurrency());
            }
            $money = $money->plus($estimate->cost);

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
