<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Checkout;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ReadCreateCheckoutView
{
    #[SerializedName('subscription_plans')]
    private array $subscriptionPlans = [];

    private array $brands = [];

    #[SerializedName('tax_types')]
    private array $taxTypes = [];

    public function getBrands(): array
    {
        return $this->brands;
    }

    public function setBrands(array $brands): void
    {
        $this->brands = $brands;
    }

    public function getSubscriptionPlans(): array
    {
        return $this->subscriptionPlans;
    }

    public function setSubscriptionPlans(array $subscriptionPlans): void
    {
        $this->subscriptionPlans = $subscriptionPlans;
    }

    public function getTaxTypes(): array
    {
        return $this->taxTypes;
    }

    public function setTaxTypes(array $taxTypes): void
    {
        $this->taxTypes = $taxTypes;
    }
}
