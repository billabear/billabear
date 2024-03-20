<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Request\App;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CancelSubscription
{
    public const WHEN_END_OF_RUN = 'end-of-run';
    public const WHEN_INSTANTLY = 'instantly';
    public const WHEN_DATE = 'specific-date';

    public const REFUND_NONE = 'none';
    public const REFUND_PRORATE = 'prorate';
    public const REFUND_FULL = 'full';

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Choice([self::WHEN_END_OF_RUN, self::WHEN_INSTANTLY, self::WHEN_DATE])]
    private $when;

    #[Assert\DateTime(format: DATE_RFC3339_EXTENDED)]
    private $date;

    #[SerializedName('refund_type')]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Choice([self::REFUND_NONE, self::REFUND_PRORATE, self::REFUND_FULL])]
    private $refundType;

    private $comment;

    public function getWhen()
    {
        return $this->when;
    }

    public function setWhen($when): void
    {
        $this->when = $when;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function getRefundType()
    {
        return $this->refundType;
    }

    public function setRefundType($refundType): void
    {
        $this->refundType = $refundType;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment): void
    {
        $this->comment = $comment;
    }
}
