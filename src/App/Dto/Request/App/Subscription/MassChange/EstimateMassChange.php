<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App\Subscription\MassChange;

use App\Validator\Constraints\BrandCodeExists;
use App\Validator\Constraints\PriceExists;
use App\Validator\Constraints\SubscriptionPlanExists;
use App\Validator\Constraints\ValidPriceChange;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback('validate')]
#[ValidPriceChange]
class EstimateMassChange
{
    #[SubscriptionPlanExists]
    #[SerializedName('target_plan')]
    private $targetPlan;

    #[SubscriptionPlanExists]
    #[SerializedName('new_plan')]
    private $newPlan;

    #[PriceExists]
    #[SerializedName('new_price')]
    private $newPrice;

    #[PriceExists]
    #[SerializedName('target_price')]
    private $targetPrice;

    #[BrandCodeExists]
    #[SerializedName('target_brand')]
    private $targetBrand;

    #[Assert\Country]
    #[SerializedName('target_country')]
    private $targetCountry;

    public function getChangeDate()
    {
        return $this->changeDate;
    }

    public function setChangeDate($changeDate): void
    {
        $this->changeDate = $changeDate;
    }

    public function getTargetPlan()
    {
        return $this->targetPlan;
    }

    public function setTargetPlan($targetPlan): void
    {
        $this->targetPlan = $targetPlan;
    }

    public function getNewPlan()
    {
        return $this->newPlan;
    }

    public function setNewPlan($newPlan): void
    {
        $this->newPlan = $newPlan;
    }

    public function getNewPrice()
    {
        return $this->newPrice;
    }

    public function setNewPrice($newPrice): void
    {
        $this->newPrice = $newPrice;
    }

    public function getTargetPrice()
    {
        return $this->targetPrice;
    }

    public function setTargetPrice($targetPrice): void
    {
        $this->targetPrice = $targetPrice;
    }

    public function getTargetBrand()
    {
        return $this->targetBrand;
    }

    public function setTargetBrand($targetBrand): void
    {
        $this->targetBrand = $targetBrand;
    }

    public function getTargetCountry()
    {
        return $this->targetCountry;
    }

    public function setTargetCountry($targetCountry): void
    {
        $this->targetCountry = $targetCountry;
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (!isset($this->targetPrice) && !isset($this->targetCountry) && !isset($this->targetPlan) && !isset($this->targetBrand)) {
            $context->buildViolation('must have at least one target criteria')->atPath('targetPrice')->addViolation();
            $context->buildViolation('must have at least one target criteria')->atPath('targetPlan')->addViolation();
            $context->buildViolation('must have at least one target criteria')->atPath('targetCountry')->addViolation();
            $context->buildViolation('must have at least one target criteria')->atPath('targetBrand')->addViolation();
        }

        if (!isset($this->newPrice) && !isset($this->newPlan)) {
            $context->buildViolation('must have at least one new price')->atPath('newPrice')->addViolation();
            $context->buildViolation('must have at least one new value')->atPath('newPlan')->addViolation();
        }
    }
}
