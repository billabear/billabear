<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Entity;

use App\Entity\Settings\NotificationSettings;
use App\Entity\Settings\OnboardingSettings;
use App\Entity\Settings\SystemSettings;
use App\Entity\Settings\TaxSettings;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'settings')]
class Settings
{
    public const DEFAULT_TAG = 'default';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'string')]
    private string $tag;

    #[ORM\Embedded(class: NotificationSettings::class)]
    private NotificationSettings $notificationSettings;

    #[ORM\Embedded(class: SystemSettings::class)]
    private SystemSettings $systemSettings;

    #[ORM\Embedded(class: OnboardingSettings::class)]
    private OnboardingSettings $onboardingSettings;

    #[ORM\Embedded(class: TaxSettings::class)]
    private TaxSettings $taxSettings;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function getNotificationSettings(): NotificationSettings
    {
        return $this->notificationSettings;
    }

    public function setNotificationSettings(NotificationSettings $notificationSettings): void
    {
        $this->notificationSettings = $notificationSettings;
    }

    public function getSystemSettings(): SystemSettings
    {
        return $this->systemSettings;
    }

    public function setSystemSettings(SystemSettings $systemSettings): void
    {
        $this->systemSettings = $systemSettings;
    }

    public function getOnboardingSettings(): OnboardingSettings
    {
        return $this->onboardingSettings;
    }

    public function setOnboardingSettings(OnboardingSettings $onboardingSettings): void
    {
        $this->onboardingSettings = $onboardingSettings;
    }

    public function getTaxSettings(): TaxSettings
    {
        return $this->taxSettings;
    }

    public function setTaxSettings(TaxSettings $taxSettings): void
    {
        $this->taxSettings = $taxSettings;
    }
}
