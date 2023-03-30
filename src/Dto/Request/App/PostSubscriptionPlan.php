<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Request\App;

use App\Dto\Generic\App\Feature;
use App\Dto\Generic\App\Price;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class PostSubscriptionPlan
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    #[SerializedName('name')]
    protected $name;

    #[Assert\Type('boolean')]
    #[SerializedName('public')]
    protected $public;

    #[Assert\Type('boolean')]
    #[SerializedName('free')]
    protected $free;

    #[Assert\Type('integer')]
    #[SerializedName('user_count')]
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
    protected array $prices = [];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPublic()
    {
        return true === $this->public;
    }

    /**
     * @param mixed $public
     */
    public function setPublic($public): void
    {
        $this->public = $public;
    }

    /**
     * @return mixed
     */
    public function getFree()
    {
        return true === $this->free;
    }

    /**
     * @param mixed $free
     */
    public function setFree($free): void
    {
        $this->free = $free;
    }

    /**
     * @return mixed
     */
    public function getUserCount()
    {
        return $this->userCount;
    }

    /**
     * @param mixed $userCount
     */
    public function setUserCount($userCount): void
    {
        $this->userCount = $userCount;
    }

    /**
     * @return mixed
     */
    public function getPerSeat()
    {
        return true === $this->perSeat;
    }

    /**
     * @param mixed $perSeat
     */
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
}
