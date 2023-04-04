<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App;

use App\Validator\Constraints\PriceExists;
use App\Validator\Constraints\SubscriptionPlanExists;
use Symfony\Component\Validator\Constraints as Assert;

class CreateSubscription
{
    #[Assert\Type('string')]
    #[SubscriptionPlanExists]
    private $subscriptionPlanId;

    #[Assert\Type('string')]
    #[PriceExists]
    private $priceId;

    /**
     * @return mixed
     */
    public function getSubscriptionPlanId()
    {
        return $this->subscriptionPlanId;
    }

    /**
     * @param mixed $subscriptionPlanId
     */
    public function setSubscriptionPlanId($subscriptionPlanId): void
    {
        $this->subscriptionPlanId = $subscriptionPlanId;
    }

    /**
     * @return mixed
     */
    public function getPriceId()
    {
        return $this->priceId;
    }

    /**
     * @param mixed $priceId
     */
    public function setPriceId($priceId): void
    {
        $this->priceId = $priceId;
    }
}
