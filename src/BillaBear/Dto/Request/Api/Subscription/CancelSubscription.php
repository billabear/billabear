<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Subscription;

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

    #[Assert\Choice([self::WHEN_END_OF_RUN, self::WHEN_INSTANTLY, self::WHEN_DATE])]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $when = 'end-of-run';

    #[Assert\Date]
    private $date;

    #[Assert\Choice([self::REFUND_NONE, self::REFUND_PRORATE, self::REFUND_FULL])]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('refund_type')]
    private $refund_type = 'none';

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
        return $this->refund_type;
    }

    public function setRefundType($refund_type): void
    {
        $this->refund_type = $refund_type;
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
