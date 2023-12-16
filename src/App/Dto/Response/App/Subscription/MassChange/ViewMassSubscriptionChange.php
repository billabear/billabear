<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\Subscription\MassChange;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewMassSubscriptionChange
{
    #[SerializedName('mass_change')]
    private MassSubscriptionChange $massSubscriptionChange;

    #[SerializedName('estimate')]
    private Estimate $estimate;

    public function getMassSubscriptionChange(): MassSubscriptionChange
    {
        return $this->massSubscriptionChange;
    }

    public function setMassSubscriptionChange(MassSubscriptionChange $massSubscriptionChange): void
    {
        $this->massSubscriptionChange = $massSubscriptionChange;
    }

    public function getEstimate(): Estimate
    {
        return $this->estimate;
    }

    public function setEstimate(Estimate $estimate): void
    {
        $this->estimate = $estimate;
    }
}
