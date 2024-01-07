<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Workflow\Places\CreateRefund;

use App\Enum\WorkflowType;
use App\Workflow\Places\PlaceInterface;

class CustomerNoticeSent implements PlaceInterface
{
    public function getName(): string
    {
        return 'customer_notice_sent';
    }

    public function getPriority(): int
    {
        return 800;
    }

    public function getWorkflow(): WorkflowType
    {
        return WorkflowType::CREATE_REFUND;
    }

    public function getToTransitionName(): string
    {
        return 'send_customer_notice';
    }
}
