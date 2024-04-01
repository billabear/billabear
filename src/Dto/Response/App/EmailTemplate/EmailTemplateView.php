<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
