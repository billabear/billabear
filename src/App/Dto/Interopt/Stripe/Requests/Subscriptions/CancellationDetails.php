<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Interopt\Stripe\Requests\Subscriptions;

use Symfony\Component\Validator\Constraints as Assert;

class CancellationDetails
{
    #[Assert\Type('string')]
    private $comment;

    #[Assert\Type('string')]
    #[Assert\Choice(['too_expensive', 'missing_features', 'switched_service', 'unused', 'customer_service', 'too_complex', 'low_quality', 'other'])]
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
