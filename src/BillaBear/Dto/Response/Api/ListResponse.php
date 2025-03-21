<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\Api;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ListResponse
{
    protected array $data = [];

    #[SerializedName('has_more')]
    protected bool $hasMore;

    #[SerializedName('last_key')]
    protected ?string $lastKey;

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

    public function getLastKey(): string
    {
        return $this->lastKey;
    }

    public function setLastKey(?string $lastId): void
    {
        $this->lastKey = $lastId;
    }
}
