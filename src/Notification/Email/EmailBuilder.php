<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Notification\Email;

use App\Entity\Customer;
use App\Notification\Email\Data\AbstractEmailData;
use Parthenon\Notification\Email;
use Twig\Environment;

class EmailBuilder
{
    public function __construct(private Environment $twig, private EmailTemplateProvider $emailTemplateProvider)
    {
    }

    public function build(Customer $customer, AbstractEmailData $emailData): Email
    {
        $emailTemplate = $this->emailTemplateProvider->getTemplateForCustomer($customer, $emailData->getTemplateName());

        $email = new Email();
        $email->setToAddress($customer->getBillingEmail());
        $email->setFromAddress($customer->getBrandSettings()->getEmailAddress());
        $email->setFromName($customer->getBrandSettings()->getBrandName());

        $templateVariables = $emailData->getData($customer, $customer->getBrandSettings());
        if ($emailTemplate->getTemplateId()) {
            $email->setTemplateName($emailTemplate->getTemplateId());
            $email->setTemplateVariables($templateVariables);
        } else {
            $twigTemplate = $this->twig->createTemplate($emailTemplate->getTemplateBody());
            $content = $this->twig->render($twigTemplate, $templateVariables);

            $email->setSubject($emailTemplate->getSubject());
            $email->setContent($content);
        }

        return $email;
    }
}
