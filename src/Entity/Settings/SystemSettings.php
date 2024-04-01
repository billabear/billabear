<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Entity\Settings;

use App\Enum\PdfGeneratorType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class SystemSettings
{
    public const DEFAULT_DUE_TIME = '30 days';

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $webhookUrl = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $invoiceNumberGeneration = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $defaultInvoiceDueTime = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subsequentialNumber = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $systemUrl = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $timezone = null;

    #[ORM\Column(type: 'boolean')]
    private bool $useStripeBilling = true;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $webhookExternalReference = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $webhookSecret = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $mainCurrency;

    #[ORM\Column(type: 'boolean')]
    private bool $updateAvailable = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $updateAvailableDismissed = false;

    #[ORM\Column(type: 'string', nullable: true, enumType: PdfGeneratorType::class)]
    private ?PdfGeneratorType $pdfGenerator = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $pdfTmpDir = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $pdfBin = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $pdfApiKey = null;

    public function getSystemUrl(): ?string
    {
        return $this->systemUrl;
    }

    public function setSystemUrl(?string $systemUrl): void
    {
        $this->systemUrl = $systemUrl;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getInvoiceNumberGeneration(): ?string
    {
        return $this->invoiceNumberGeneration;
    }

    public function setInvoiceNumberGeneration(?string $invoiceNumberGeneration): void
    {
        $this->invoiceNumberGeneration = $invoiceNumberGeneration;
    }

    public function isUseStripeBilling(): bool
    {
        return $this->useStripeBilling;
    }

    public function setUseStripeBilling(bool $useStripeBilling): void
    {
        $this->useStripeBilling = $useStripeBilling;
    }

    public function getWebhookExternalReference(): ?string
    {
        return $this->webhookExternalReference;
    }

    public function setWebhookExternalReference(?string $webhookExternalReference): void
    {
        $this->webhookExternalReference = $webhookExternalReference;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(?string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getMainCurrency(): string
    {
        return $this->mainCurrency;
    }

    public function setMainCurrency(string $mainCurrency): void
    {
        $this->mainCurrency = $mainCurrency;
    }

    public function getWebhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    public function setWebhookSecret(?string $webhookSecret): void
    {
        $this->webhookSecret = $webhookSecret;
    }

    public function isUpdateAvailable(): bool
    {
        return $this->updateAvailable;
    }

    public function setUpdateAvailable(bool $updateAvailable): void
    {
        $this->updateAvailable = $updateAvailable;
    }

    public function getUpdateAvailableDismissed(): bool
    {
        return true === $this->updateAvailableDismissed;
    }

    public function setUpdateAvailableDismissed(?bool $updateAvailableDismissed): void
    {
        $this->updateAvailableDismissed = $updateAvailableDismissed;
    }

    public function getSubsequentialNumber(): ?int
    {
        return $this->subsequentialNumber;
    }

    public function setSubsequentialNumber(?int $subsequentialNumber): void
    {
        $this->subsequentialNumber = $subsequentialNumber;
    }

    public function getDefaultInvoiceDueTime(): ?string
    {
        return $this->defaultInvoiceDueTime;
    }

    public function setDefaultInvoiceDueTime(?string $defaultInvoiceDueTime): void
    {
        $this->defaultInvoiceDueTime = $defaultInvoiceDueTime;
    }

    public function getPdfGenerator(): ?PdfGeneratorType
    {
        return $this->pdfGenerator;
    }

    public function setPdfGenerator(?PdfGeneratorType $pdfGenerator): void
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public function getPdfTmpDir(): ?string
    {
        return $this->pdfTmpDir;
    }

    public function setPdfTmpDir(?string $pdfTmpDir): void
    {
        $this->pdfTmpDir = $pdfTmpDir;
    }

    public function getPdfBin(): ?string
    {
        return $this->pdfBin;
    }

    public function setPdfBin(?string $pdfBin): void
    {
        $this->pdfBin = $pdfBin;
    }

    public function getPdfApiKey(): ?string
    {
        return $this->pdfApiKey;
    }

    public function setPdfApiKey(?string $pdfApiKey): void
    {
        $this->pdfApiKey = $pdfApiKey;
    }
}
