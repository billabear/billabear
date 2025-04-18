<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Subscription\MassChange;

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
