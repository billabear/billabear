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

    private $comment = null;

    /**
     * @return mixed
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * @param mixed $when
     */
    public function setWhen($when): void
    {
        $this->when = $when;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getRefundType()
    {
        return $this->refundType;
    }

    /**
     * @param mixed $refundType
     */
    public function setRefundType($refundType): void
    {
        $this->refundType = $refundType;
    }

    /**
     * @return null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param null $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }
}
