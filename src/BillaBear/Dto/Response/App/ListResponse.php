<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App;

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

    #[SerializedName('extra_data')]
    protected array $extraData;

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

    public function getExtraData(): array
    {
        return $this->extraData;
    }

    public function setExtraData(array $extraData): void
    {
        $this->extraData = $extraData;
    }
}
