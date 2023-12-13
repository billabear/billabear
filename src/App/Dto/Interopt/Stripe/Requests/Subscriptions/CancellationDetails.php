<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
