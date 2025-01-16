<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

class Limit
{
    protected Feature $feature;

    protected int $limit = 0;

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
