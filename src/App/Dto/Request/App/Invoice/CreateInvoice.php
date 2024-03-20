<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Invoice;

use App\Validator\Constraints\CustomerExists;
use App\Validator\Constraints\SamePaymentSchedule;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateInvoice
{
    #[Assert\NotBlank()]
    #[CustomerExists]
    private $customer;

    #[Assert\Valid]
    private $items = [];

    #[Assert\Valid]
    #[SamePaymentSchedule]
    private $subscriptions = [];

    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    #[SerializedName('due_date')]
    private $dueDate;

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items): void
    {
        $this->items = $items;
    }

    public function addItem(CreateInvoiceItem $createInvoiceItem): void
    {
        $this->items[] = $createInvoiceItem;
    }

    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function setSubscriptions($subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function addSubscription(CreateInvoiceSubscription $createInvoiceSubscription): void
    {
        $this->subscriptions[] = $createInvoiceSubscription;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function setDueDate($dueDate): void
    {
        $this->dueDate = $dueDate;
    }
}
