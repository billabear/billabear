<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email;

use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Notification\Email\Data\AbstractEmailData;
use Twig\Environment;

class EmailBuilder
{
    public function __construct(
        private Environment $twig,
        private EmailTemplateProvider $emailTemplateProvider,
    ) {
    }

    public function build(Customer $customer, AbstractEmailData $emailData): Email
    {
        $emailTemplate = $this->emailTemplateProvider->getTemplateForCustomer($customer, $emailData->getTemplateName());

        return $this->buildWithTemplate($customer, $emailTemplate, $emailData);
    }

    public function buildWithTemplate(Customer $customer, EmailTemplate $emailTemplate, AbstractEmailData $emailData): Email
    {
        $email = new Email();
        $email->setBillabearEmail($emailTemplate->getName());
        $email->setCustomerId((string) $customer->getId());
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
