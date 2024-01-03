<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
