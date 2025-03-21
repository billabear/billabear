<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Settings\User;

use Symfony\Component\Serializer\Annotation\SerializedName;

class UserListView
{
    #[SerializedName('has_more')]
    protected bool $hasMore;

    #[SerializedName('last_key')]
    protected ?string $lastKey;

    #[SerializedName('first_key')]
    protected ?string $firstKey;
    private array $users;

    private array $invites;

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    public function getInvites(): array
    {
        return $this->invites;
    }

    public function setInvites(array $invites): void
    {
        $this->invites = $invites;
    }

    public function isHasMore(): bool
    {
        return $this->hasMore;
    }

    public function setHasMore(bool $hasMore): void
    {
        $this->hasMore = $hasMore;
    }

    public function getLastKey(): ?string
    {
        return $this->lastKey;
    }

    public function setLastKey(?string $lastKey): void
    {
        $this->lastKey = $lastKey;
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
