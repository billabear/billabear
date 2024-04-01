<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'email_templates')]
#[ORM\Index(name: 'name_locale', fields: ['name', 'locale'])]
#[ORM\UniqueConstraint(name: 'name_locale_brand', fields: ['name', 'locale', 'brand'])]
class EmailTemplate
{
    public const NAME_SUBSCRIPTION_CREATED = 'subscription_created';
    public const NAME_SUBSCRIPTION_PAUSED = 'subscription_paused';
    public const NAME_SUBSCRIPTION_CANCELLED = 'subscription_cancelled';

    public const NAME_PAYMENT_SUCCEEDED = 'payment_succeeded';
    public const NAME_PAYMENT_FAILED = 'payment_failed';
    public const NAME_PAYMENT_FAILURE_WARNING = 'payment_failure_warning';

    public const NAME_PAYMENT_METHOD_EXPIRY_WARNING = 'payment_method_expiry_warning';
    public const NAME_PAYMENT_METHOD_DAY_BEFORE_WARNING = 'payment_method_expiry_warning_day_before_still_valid';
    public const NAME_PAYMENT_METHOD_DAY_BEFORE_NOT_VALID_WARNING = 'payment_method_expiry_warning_day_before_no_longer_valid';

    public const NAME_PAYMENT_METHOD_NO_VALID_METHODS = 'payment_method_no_valid_methods';

    public const NAME_INVOICE_CREATED = 'invoice_created';
    public const NAME_INVOICE_OVERDUE = 'invoice_overdue';

    public const NAME_QUOTE_CREATED = 'quote_created';

    public const TEMPLATE_NAMES = [
        self::NAME_SUBSCRIPTION_CREATED,
        self::NAME_SUBSCRIPTION_PAUSED,
        self::NAME_SUBSCRIPTION_CANCELLED,
        self::NAME_PAYMENT_SUCCEEDED,
        self::NAME_PAYMENT_FAILED,
        self::NAME_PAYMENT_FAILURE_WARNING,
        self::NAME_PAYMENT_METHOD_EXPIRY_WARNING,
        self::NAME_PAYMENT_METHOD_NO_VALID_METHODS,
        self::NAME_INVOICE_CREATED,
        self::NAME_INVOICE_OVERDUE,
        self::NAME_QUOTE_CREATED,
    ];

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $locale;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $useEmspTemplate;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $templateId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $templateBody = null;

    #[ORM\ManyToOne(targetEntity: BrandSettings::class)]
    private BrandSettings $brand;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function isUseEmspTemplate(): bool
    {
        return $this->useEmspTemplate;
    }

    public function setUseEmspTemplate(bool $useEmspTemplate): void
    {
        $this->useEmspTemplate = $useEmspTemplate;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function setTemplateId(?string $templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getTemplateBody(): ?string
    {
        return $this->templateBody;
    }

    public function setTemplateBody(?string $templateBody): void
    {
        $this->templateBody = $templateBody;
    }

    public function getBrand(): BrandSettings
    {
        return $this->brand;
    }

    public function setBrand(BrandSettings $brand): void
    {
        $this->brand = $brand;
    }
}
