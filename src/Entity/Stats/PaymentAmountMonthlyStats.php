<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity\Stats;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table('stats_payment_amount_monthly')]
class PaymentAmountMonthlyStats extends AbstractMoneyStat
{
}
