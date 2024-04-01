<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
