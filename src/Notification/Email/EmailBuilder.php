<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
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
        $emailTemplate = $this->emailTemplateProvider->createTemplateForCustomer($customer, $emailData->getTemplateName());

        $email = new Email();
        $email->setToAddress($customer->getBillingEmail());
        $email->setFromAddress($customer->getBrandSettings()->getEmailAddress());
        $email->setFromName($customer->getBrandSettings()->getBrandName());

        if ($emailTemplate->getTemplateId()) {
            $email->setTemplateName($emailTemplate->getTemplateId());
            $email->setTemplateVariables($emailData->getData());
        } else {
            $twigTemplate = $this->twig->createTemplate($emailTemplate->getTemplateBody());
            $content = $this->twig->render($twigTemplate, $emailData->getData());

            $email->setSubject($emailTemplate->getSubject());
            $email->setContent($content);
        }

        return $email;
    }
}
