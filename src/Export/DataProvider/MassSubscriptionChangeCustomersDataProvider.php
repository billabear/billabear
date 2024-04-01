<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Export\DataProvider;

use App\Repository\MassSubscriptionChangeRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\ExportRequest;

class MassSubscriptionChangeCustomersDataProvider implements DataProviderInterface
{
    public function __construct(
        private MassSubscriptionChangeRepositoryInterface $massSubscriptionChangeRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function getData(ExportRequest $exportRequest): iterable
    {
        $parameters = $exportRequest->getDataProviderParameters();

        if (!isset($parameters['mass_change_id'])) {
            throw new \Exception('Mass change must be given');
        }

        $massChange = $this->massSubscriptionChangeRepository->findById($parameters['mass_change_id']);

        $results = $this->subscriptionRepository->findMassChangable(
            $massChange->getTargetSubscriptionPlan(),
            $massChange->getTargetPrice(),
            $massChange->getBrandSettings(),
            $massChange->getTargetCountry(),
        );

        foreach ($results as $result) {
            yield $result->getCustomer();
        }
    }
}
