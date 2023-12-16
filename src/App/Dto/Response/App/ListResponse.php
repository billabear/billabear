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

namespace App\Dto\Response\App;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ListResponse
{
    protected array $data = [];

    #[SerializedName('has_more')]
    protected bool $hasMore;

    #[SerializedName('last_key')]
    protected $lastKey;

    #[SerializedName('first_key')]
    protected $firstKey;

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function isHasMore(): bool
    {
        return $this->hasMore;
    }

    public function setHasMore(bool $hasMore): void
    {
        $this->hasMore = $hasMore;
    }

    public function getLastKey()
    {
        return $this->lastKey;
    }

    /**
     * @param string $lastId
     */
    public function setLastKey($lastId): void
    {
        $this->lastKey = $lastId;
    }

    public function getFirstKey()
    {
        return $this->firstKey;
    }

    public function setFirstKey($firstKey): void
    {
        $this->firstKey = $firstKey;
    }
}
