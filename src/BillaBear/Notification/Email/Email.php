<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email;

class Email extends \Parthenon\Notification\Email
{
    private ?string $billabearEmail = null;

    private ?string $customerId = null;

    public function getBillabearEmail(): ?string
    {
        return $this->billabearEmail;
    }

    public function setBillabearEmail(?string $billabearEmail): void
    {
        $this->billabearEmail = $billabearEmail;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }
}
