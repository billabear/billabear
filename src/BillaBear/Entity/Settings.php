<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Entity;

use BillaBear\Entity\Settings\AccountingIntegration;
use BillaBear\Entity\Settings\CustomerSupportIntegration;
use BillaBear\Entity\Settings\NewsletterIntegration;
use BillaBear\Entity\Settings\NotificationSettings;
use BillaBear\Entity\Settings\OnboardingSettings;
use BillaBear\Entity\Settings\SystemSettings;
use BillaBear\Entity\Settings\TaxSettings;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'settings')]
class Settings
{
    public const DEFAULT_TAG = 'default';

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Id]
    private $id;

    #[ORM\Column(type: 'string')]
    private string $tag;

    #[ORM\Embedded(class: NotificationSettings::class)]
    private NotificationSettings $notificationSettings;

    #[ORM\Embedded(class: AccountingIntegration::class)]
    private AccountingIntegration $accountingIntegration;

    #[ORM\Embedded(class: CustomerSupportIntegration::class)]
    private CustomerSupportIntegration $customerSupportIntegration;

    #[ORM\Embedded(class: NewsletterIntegration::class)]
    private NewsletterIntegration $newsletterIntegration;

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

    public function getAccountingIntegration(): AccountingIntegration
    {
        if (!isset($this->accountingIntegration)) {
            $this->accountingIntegration = new AccountingIntegration();
        }

        return $this->accountingIntegration;
    }

    public function setAccountingIntegration(AccountingIntegration $accountingIntegration): void
    {
        $this->accountingIntegration = $accountingIntegration;
    }

    public function getCustomerSupportIntegration(): CustomerSupportIntegration
    {
        if (!isset($this->customerSupportIntegration)) {
            $this->customerSupportIntegration = new CustomerSupportIntegration();
        }

        return $this->customerSupportIntegration;
    }

    public function setCustomerSupportIntegration(CustomerSupportIntegration $customerSupportIntegration): void
    {
        $this->customerSupportIntegration = $customerSupportIntegration;
    }

    public function getNewsletterIntegration(): NewsletterIntegration
    {
        if (!isset($this->newsletterIntegration)) {
            $this->newsletterIntegration = new NewsletterIntegration();
        }

        return $this->newsletterIntegration;
    }

    public function setNewsletterIntegration(NewsletterIntegration $newsletterIntegration): void
    {
        $this->newsletterIntegration = $newsletterIntegration;
    }
}
