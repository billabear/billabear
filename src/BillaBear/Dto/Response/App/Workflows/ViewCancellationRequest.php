<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Workflows;

use BillaBear\Dto\Generic\App\CancellationRequest;
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
