<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\Workflows;

use App\Dto\Generic\App\CancellationRequest;
use Symfony\Component\Serializer\Annotation\SerializedName;

class ViewCancellationRequest
{
    #[SerializedName('cancellation_request')]
    protected CancellationRequest $cancellationRequest;

    public function getCancellationRequest(): CancellationRequest
    {
        return $this->cancellationRequest;
    }

    public function setCancellationRequest(CancellationRequest $cancellationRequest): void
    {
        $this->cancellationRequest = $cancellationRequest;
    }
}
