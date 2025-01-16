<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity\Stats;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('stats_customer_creation_monthly')]
class CustomerCreationMonthlyStats extends AbstractStats
{
}
