<?php

namespace App\Dto\Response;

class ListResponse
{
    protected array $data = [];

    protected bool $hasMore;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function isHasMore(): bool
    {
        return $this->hasMore;
    }

    /**
     * @param bool $hasMore
     */
    public function setHasMore(bool $hasMore): void
    {
        $this->hasMore = $hasMore;
    }


}