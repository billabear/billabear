<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api\Customer;

use BillaBear\DataMappers\Usage\UsageLimitDataMapper;
use BillaBear\Repository\UsageLimitRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UsageLimitsController
{
    use LoggerAwareTrait;

    #[Route('/api/v1/customer/{id}/usage-limits', name: 'api_v1_customer_read_usage_limits', methods: ['GET'])]
    public function viewUsageLimits(
        Request $request,
        UsageLimitRepositoryInterface $usageLimitRepository,
        UsageLimitDataMapper $usageLimitDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received an API request to list usage limits for a customer', ['customer_id' => $request->get('id')]);

        return new JsonResponse([]);
    }
}
