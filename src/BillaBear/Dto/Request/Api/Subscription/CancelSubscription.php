<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\Api\Subscription;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CancelSubscription
{
    public const WHEN_END_OF_RUN = 'end-of-run';
    public const WHEN_INSTANTLY = 'instantly';
    public const WHEN_DATE = 'specific-date';

    public const REFUND_NONE = 'none';
    public const REFUND_PRORATE = 'prorate';
    public const REFUND_FULL = 'full';

    public function __construct(
        #[Assert\Choice([self::WHEN_END_OF_RUN, self::WHEN_INSTANTLY, self::WHEN_DATE])]
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $when = 'end-of-run',

        #[Assert\Date]
        public ?string $date = null,

        #[Assert\Choice([self::REFUND_NONE, self::REFUND_PRORATE, self::REFUND_FULL])]
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[SerializedName('refund_type')]
        public string $refund_type = 'none',

        public ?string $comment = null,
    ) {
    }
}
