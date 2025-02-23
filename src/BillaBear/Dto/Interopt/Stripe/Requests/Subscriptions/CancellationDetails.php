<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Interopt\Stripe\Requests\Subscriptions;

use Symfony\Component\Validator\Constraints as Assert;

class CancellationDetails
{
    #[Assert\Type('string')]
    private $comment;

    #[Assert\Choice(['too_expensive', 'missing_features', 'switched_service', 'unused', 'customer_service', 'too_complex', 'low_quality', 'other'])]
    #[Assert\Type('string')]
    private $feedback;

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    public function getFeedback()
    {
        return $this->feedback;
    }

    public function setFeedback($feedback): void
    {
        $this->feedback = $feedback;
    }
}
