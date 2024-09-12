<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\SubscriptionPlan;

/**
 * @method Price|null       getPrice()
 * @method Customer         getCustomer()
 * @method SubscriptionPlan getSubscriptionPlan()
 */
#[ORM\Entity]
#[ORM\Table('subscription')]
class Subscription extends \Parthenon\Billing\Entity\Subscription
{
}
