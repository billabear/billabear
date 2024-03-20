<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App;

use App\Dto\Generic\App\Feature;
use App\Dto\Generic\App\Price;
use App\Validator\Constraints\UniqueSubscriptionPlanCodeName;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class PostSubscriptionPlan
{
    protected $id;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[SerializedName('name')]
    protected $name;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Regex(pattern: '~^[a-z0-9_]+$~', message: 'Only lower case, underscores and numbers allowed')]
    #[SerializedName('code_name')]
    #[UniqueSubscriptionPlanCodeName]
    protected $codeName;

    #[Assert\Type('boolean')]
    #[SerializedName('public')]
    protected $public;

    #[Assert\Type('boolean')]
    #[SerializedName('free')]
    protected $free;

    #[Assert\Type('boolean')]
    #[SerializedName('has_trial')]
    protected $hasTrial;

    #[Assert\Type('integer')]
    #[Assert\PositiveOrZero]
    #[SerializedName('trial_length_days')]
    protected $trialLengthDays;

    #[Assert\Type('integer')]
    #[SerializedName('user_count')]
    #[Assert\NotBlank()]
    #[Assert\PositiveOrZero]
    protected $userCount;

    #[Assert\Type('boolean')]
    #[SerializedName('per_seat')]
    protected $perSeat;

    #[SerializedName('limits')]
    #[Assert\Valid]
    protected $limits = [];

    #[SerializedName('features')]
    protected $features = [];

    #[SerializedName('prices')]
    #[Assert\Count(min: 1)]
    protected array $prices = [];

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getCodeName()
    {
        return $this->codeName;
    }

    public function setCodeName($codeName): void
    {
        $this->codeName = $codeName;
    }

    public function getPublic()
    {
        return true === $this->public;
    }

    public function setPublic($public): void
    {
        $this->public = $public;
    }

    public function getFree()
    {
        return true === $this->free;
    }

    public function setFree($free): void
    {
        $this->free = $free;
    }

    public function getUserCount()
    {
        return $this->userCount;
    }

    public function setUserCount($userCount): void
    {
        $this->userCount = $userCount;
    }

    public function getPerSeat()
    {
        return true === $this->perSeat;
    }

    public function setPerSeat($perSeat): void
    {
        $this->perSeat = $perSeat;
    }

    public function getLimits(): array
    {
        return $this->limits;
    }

    public function setLimits(array $limits): void
    {
        $this->limits = $limits;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    public function getPrices(): array
    {
        return $this->prices;
    }

    public function setPrices(array $prices): void
    {
        $this->prices = $prices;
    }

    public function addFeature(Feature $feature)
    {
        $this->features[] = $feature;
    }

    public function addPrice(Price $price)
    {
        $this->prices[] = $price;
    }

    public function addLimit(PostLimit $limit)
    {
        $this->limits[] = $limit;
    }

    public function getHasTrial()
    {
        return $this->hasTrial;
    }

    public function setHasTrial($hasTrial): void
    {
        $this->hasTrial = $hasTrial;
    }

    public function getTrialLengthDays()
    {
        return $this->trialLengthDays;
    }

    public function setTrialLengthDays($trialLengthDays): void
    {
        $this->trialLengthDays = $trialLengthDays;
    }
}
