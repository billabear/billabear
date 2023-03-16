<?php

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
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
    protected ?string $lastKey;

    #[SerializedName('first_key')]
    protected ?string $firstKey;

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

    /**
     * @param string $lastId
     */
    public function setLastKey(?string $lastId): void
    {
        $this->lastKey = $lastId;
    }

    public function getFirstKey(): ?string
    {
        return $this->firstKey;
    }

    public function setFirstKey(?string $firstKey): void
    {
        $this->firstKey = $firstKey;
    }
}
