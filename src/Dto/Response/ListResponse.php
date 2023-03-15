<?php

namespace App\Dto\Response;

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
