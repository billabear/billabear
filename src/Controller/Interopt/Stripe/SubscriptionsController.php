<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 09.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\Interopt\Stripe;

use App\DataMappers\Interopt\Stripe\SubscriptionDataMapper;
use App\Dto\Interopt\Stripe\Models\ListModel;
use App\Repository\SubscriptionRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SubscriptionsController
{
    #[Route('/interopt/stripe/v1/subscriptions', name: 'app_interopt_stripe_subscriptions_list', methods: ['GET'])]
    public function listAction(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $firstId = $request->get('ending_before');
        $lastId = $request->get('starting_after');
        $limit = $request->get('limit', 25);

        $subscriptions = $subscriptionRepository->getList([], limit: $limit, lastId: $lastId, firstId: $firstId);

        $subscriptionModels = array_map([$subscriptionDataMapper, 'createModel'], $subscriptions->getResults());

        $output = new ListModel();
        $output->setData($subscriptionModels);
        $output->setUrl($request->getUri());
        $output->setHasMore($subscriptions->hasMore());

        $json = $serializer->serialize($output, 'json');

        return new JsonResponse($json, json: true);
    }
}
