<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Export\DataProvider;

use BillaBear\Repository\MassSubscriptionChangeRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
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
