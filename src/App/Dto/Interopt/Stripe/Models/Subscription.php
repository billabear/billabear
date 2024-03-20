<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Interopt\Stripe\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Subscription
{
    #[SerializedName('id')]
    private string $id;

    #[SerializedName('object')]
    private string $object = 'subscription';

    #[SerializedName('cancel_at_period_end')]
    private ?bool $cancelAtPeriodEnd = null;

    #[SerializedName('currency')]
    private ?string $currency = null;

    #[SerializedName('current_period_end')]
    private ?int $currentPeriodEnd = null;

    #[SerializedName('current_period_start')]
    private ?int $currentPeriodStart = null;

    #[SerializedName('customer')]
    private ?string $customer = null;

    #[SerializedName('default_payment_method')]
    private ?string $defaultPaymentMethod = null;

    #[SerializedName('description')]
    private ?string $description = null;

    #[SerializedName('items')]
    private ?ListModel $items = null;

    #[SerializedName('last_invoice')]
    private ?string $lastInvoice = null;

    #[SerializedName('metadata')]
    private ?array $metadata = null;

    #[SerializedName('pending_setup_intent')]
    private ?string $pendingSetupIntent = null;

    #[SerializedName('pending_update')]
    private ?array $pendingUpdate = null;

    private ?string $status = null;

    private ?string $application = null;

    #[SerializedName('automatic_tax')]
    private ?array $automaticTax = [];

    #[SerializedName('billing_cycle_anchor')]
    private ?int $billingCycleAnchor = null;

    #[SerializedName('billing_thresholds')]
    private ?array $billingThresholds = null;

    #[SerializedName('cancel_at')]
    private ?int $cancelAt = null;

    #[SerializedName('collection_method')]
    private ?string $collectionMethod = null;

    private ?int $created = null;

    #[SerializedName('days_until_due')]
    private ?int $daysUntilDue = null;

    #[SerializedName('default_source')]
    private ?string $defaultSource;

    #[SerializedName('default_tax_rates')]
    private ?array $defaultTaxRates = null;

    private ?array $discount = null;

    #[SerializedName('ended_at')]
    private ?int $endedAt = null;

    #[SerializedName('next_pending_invoice_item_invoice')]
    private ?int $nextPendingInvoiceItemInvoice = null;

    #[SerializedName('on_behalf_of')]
    private ?string $onBehalfOf = null;

    #[SerializedName('pause_collection')]
    private ?array $pauseCollection = null;

    #[SerializedName('payment_settings')]
    private ?array $paymentSettings = null;

    #[SerializedName('pending_invoice_item_interval')]
    private ?array $pendingInvoiceItemInterval = [];

    private ?string $schedule = null;

    #[SerializedName('start_date')]
    private ?int $startDate = null;

    #[SerializedName('test_clock')]
    private ?string $testClock = null;

    #[SerializedName('trial_end')]
    private ?int $trialEnd = null;

    #[SerializedName('trial_settings')]
    private ?array $trialSettings = null;

    #[SerializedName('trial_start')]
    private ?int $trialStart = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function setObject(string $object): void
    {
        $this->object = $object;
    }

    public function getCancelAtPeriodEnd(): ?bool
    {
        return $this->cancelAtPeriodEnd;
    }

    public function setCancelAtPeriodEnd(?bool $cancelAtPeriodEnd): void
    {
        $this->cancelAtPeriodEnd = $cancelAtPeriodEnd;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrentPeriodEnd(): ?int
    {
        return $this->currentPeriodEnd;
    }

    public function setCurrentPeriodEnd(?int $currentPeriodEnd): void
    {
        $this->currentPeriodEnd = $currentPeriodEnd;
    }

    public function getCurrentPeriodStart(): ?int
    {
        return $this->currentPeriodStart;
    }

    public function setCurrentPeriodStart(?int $currentPeriodStart): void
    {
        $this->currentPeriodStart = $currentPeriodStart;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): void
    {
        $this->customer = $customer;
    }

    public function getDefaultPaymentMethod(): ?string
    {
        return $this->defaultPaymentMethod;
    }

    public function setDefaultPaymentMethod(?string $defaultPaymentMethod): void
    {
        $this->defaultPaymentMethod = $defaultPaymentMethod;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getItems(): ?ListModel
    {
        return $this->items;
    }

    public function setItems(?ListModel $items): void
    {
        $this->items = $items;
    }

    public function getLastInvoice(): ?string
    {
        return $this->lastInvoice;
    }

    public function setLastInvoice(?string $lastInvoice): void
    {
        $this->lastInvoice = $lastInvoice;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getPendingSetupIntent(): ?string
    {
        return $this->pendingSetupIntent;
    }

    public function setPendingSetupIntent(?string $pendingSetupIntent): void
    {
        $this->pendingSetupIntent = $pendingSetupIntent;
    }

    public function getPendingUpdate(): ?array
    {
        return $this->pendingUpdate;
    }

    public function setPendingUpdate(?array $pendingUpdate): void
    {
        $this->pendingUpdate = $pendingUpdate;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getApplication(): ?string
    {
        return $this->application;
    }

    public function setApplication(?string $application): void
    {
        $this->application = $application;
    }

    public function getAutomaticTax(): ?array
    {
        return $this->automaticTax;
    }

    public function setAutomaticTax(?array $automaticTax): void
    {
        $this->automaticTax = $automaticTax;
    }

    public function getBillingCycleAnchor(): ?int
    {
        return $this->billingCycleAnchor;
    }

    public function setBillingCycleAnchor(?int $billingCycleAnchor): void
    {
        $this->billingCycleAnchor = $billingCycleAnchor;
    }

    public function getBillingThresholds(): ?array
    {
        return $this->billingThresholds;
    }

    public function setBillingThresholds(?array $billingThresholds): void
    {
        $this->billingThresholds = $billingThresholds;
    }

    public function getCancelAt(): ?int
    {
        return $this->cancelAt;
    }

    public function setCancelAt(?int $cancelAt): void
    {
        $this->cancelAt = $cancelAt;
    }

    public function getCollectionMethod(): ?string
    {
        return $this->collectionMethod;
    }

    public function setCollectionMethod(?string $collectionMethod): void
    {
        $this->collectionMethod = $collectionMethod;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(?int $created): void
    {
        $this->created = $created;
    }

    public function getDaysUntilDue(): ?int
    {
        return $this->daysUntilDue;
    }

    public function setDaysUntilDue(?int $daysUntilDue): void
    {
        $this->daysUntilDue = $daysUntilDue;
    }

    public function getDefaultSource(): ?string
    {
        return $this->defaultSource;
    }

    public function setDefaultSource(?string $defaultSource): void
    {
        $this->defaultSource = $defaultSource;
    }

    public function getDefaultTaxRates(): ?array
    {
        return $this->defaultTaxRates;
    }

    public function setDefaultTaxRates(?array $defaultTaxRates): void
    {
        $this->defaultTaxRates = $defaultTaxRates;
    }

    public function getDiscount(): ?array
    {
        return $this->discount;
    }

    public function setDiscount(?array $discount): void
    {
        $this->discount = $discount;
    }

    public function getEndedAt(): ?int
    {
        return $this->endedAt;
    }

    public function setEndedAt(?int $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function getNextPendingInvoiceItemInvoice(): ?int
    {
        return $this->nextPendingInvoiceItemInvoice;
    }

    public function setNextPendingInvoiceItemInvoice(?int $nextPendingInvoiceItemInvoice): void
    {
        $this->nextPendingInvoiceItemInvoice = $nextPendingInvoiceItemInvoice;
    }

    public function getOnBehalfOf(): ?string
    {
        return $this->onBehalfOf;
    }

    public function setOnBehalfOf(?string $onBehalfOf): void
    {
        $this->onBehalfOf = $onBehalfOf;
    }

    public function getPauseCollection(): ?array
    {
        return $this->pauseCollection;
    }

    public function setPauseCollection(?array $pauseCollection): void
    {
        $this->pauseCollection = $pauseCollection;
    }

    public function getPaymentSettings(): ?array
    {
        return $this->paymentSettings;
    }

    public function setPaymentSettings(?array $paymentSettings): void
    {
        $this->paymentSettings = $paymentSettings;
    }

    public function getPendingInvoiceItemInterval(): ?array
    {
        return $this->pendingInvoiceItemInterval;
    }

    public function setPendingInvoiceItemInterval(?array $pendingInvoiceItemInterval): void
    {
        $this->pendingInvoiceItemInterval = $pendingInvoiceItemInterval;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function getStartDate(): ?int
    {
        return $this->startDate;
    }

    public function setStartDate(?int $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getTestClock(): ?string
    {
        return $this->testClock;
    }

    public function setTestClock(?string $testClock): void
    {
        $this->testClock = $testClock;
    }

    public function getTrialEnd(): ?int
    {
        return $this->trialEnd;
    }

    public function setTrialEnd(?int $trialEnd): void
    {
        $this->trialEnd = $trialEnd;
    }

    public function getTrialSettings(): ?array
    {
        return $this->trialSettings;
    }

    public function setTrialSettings(?array $trialSettings): void
    {
        $this->trialSettings = $trialSettings;
    }

    public function getTrialStart(): ?int
    {
        return $this->trialStart;
    }

    public function setTrialStart(?int $trialStart): void
    {
        $this->trialStart = $trialStart;
    }
}
