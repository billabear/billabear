<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Billing\Entity\Price;

/**
 * @method Price getPrice()
 */
#[ORM\Entity]
#[ORM\Table('price_tier_component')]
class TierComponent extends \Parthenon\Billing\Entity\TierComponent
{
}
