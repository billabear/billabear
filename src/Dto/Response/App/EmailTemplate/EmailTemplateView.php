<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Dto\Response\App\EmailTemplate;

use Symfony\Component\Serializer\Annotation\SerializedName;

class EmailTemplateView
{
    #[SerializedName('email_template')]
    private FullEmailTemplate $emailTemplate;

    public function getEmailTemplate(): FullEmailTemplate
    {
        return $this->emailTemplate;
    }

    public function setEmailTemplate(FullEmailTemplate $emailTemplate): void
    {
        $this->emailTemplate = $emailTemplate;
    }
}
