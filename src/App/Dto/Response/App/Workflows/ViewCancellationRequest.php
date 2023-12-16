<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
