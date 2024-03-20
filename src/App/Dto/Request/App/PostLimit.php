<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
