<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App;

use App\Dto\Generic\App\Feature;
use Symfony\Component\Validator\Constraints as Assert;

class PostLimit
{
    protected Feature $feature;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    protected $limit;

    public function getFeature(): Feature
    {
        return $this->feature;
    }

    public function setFeature(Feature $feature): void
    {
        $this->feature = $feature;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }
}
