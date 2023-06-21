<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity\BrandSettings;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class NotificationSettings
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $subscriptionCreation = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $subscriptionCancellation = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $expiringCardWarning = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $expiringCardDayBefore = null;

    public function getSubscriptionCreation(): bool
    {
        return true === $this->subscriptionCreation;
    }

    public function setSubscriptionCreation(?bool $subscriptionCreation): void
    {
        $this->subscriptionCreation = $subscriptionCreation;
    }

    public function getSubscriptionCancellation(): bool
    {
        return true === $this->subscriptionCancellation;
    }

    public function setSubscriptionCancellation(?bool $subscriptionCancellation): void
    {
        $this->subscriptionCancellation = $subscriptionCancellation;
    }

    public function getExpiringCardWarning(): bool
    {
        return true === $this->expiringCardWarning;
    }

    public function setExpiringCardWarning(?bool $expiringCardWarning): void
    {
        $this->expiringCardWarning = $expiringCardWarning;
    }

    public function getExpiringCardDayBefore(): bool
    {
        return true === $this->expiringCardDayBefore;
    }

    public function setExpiringCardDayBefore(?bool $expiringCardDayBefore): void
    {
        $this->expiringCardDayBefore = $expiringCardDayBefore;
    }
}
