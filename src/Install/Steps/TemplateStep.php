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

namespace App\Install\Steps;

use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Entity\Template;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\EmailTemplateRepositoryInterface;
use App\Repository\TemplateRepositoryInterface;

class TemplateStep
{
    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        private EmailTemplateRepositoryInterface $emailTemplateRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
    ) {
    }

    public function install()
    {
        $brand = $this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);

        $template = new Template();
        $template->setBrand(Customer::DEFAULT_BRAND);
        $template->setName(Template::NAME_RECEIPT);
        $template->setContent('template here');

        $this->templateRepository->save($template);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_SUBSCRIPTION_CREATED);
        $emailTemplate->setSubject('Subscription Created');
        $emailTemplate->setTemplateBody('Subscription Created');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_SUCCEEDED);
        $emailTemplate->setSubject('Payment Received');
        $emailTemplate->setTemplateBody('Thanks for your payment. Here is the receipt.');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_FAILED);
        $emailTemplate->setSubject('Payment Failed');
        $emailTemplate->setTemplateBody('Thanks for your payment. Here is the receipt.');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_FAILURE_WARNING);
        $emailTemplate->setSubject('Payment Failed');
        $emailTemplate->setTemplateBody('Thanks for your payment. Here is the receipt. We\'ll try and charge you later.');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_SUBSCRIPTION_CANCELLED);
        $emailTemplate->setSubject('Subscription Cancelled');
        $emailTemplate->setTemplateBody('Your subscription has been cancelled');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_SUBSCRIPTION_PAUSED);
        $emailTemplate->setSubject('Subscription Paused');
        $emailTemplate->setTemplateBody('Your subscription has been paused');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_METHOD_NO_VALID_METHODS);
        $emailTemplate->setSubject('You have no valid payment methods');
        $emailTemplate->setTemplateBody('There are no valid payment methods attached to your account. Please add one.');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_METHOD_EXPIRY_WARNING);
        $emailTemplate->setSubject('Payment Method Expiring Soon');
        $emailTemplate->setTemplateBody('Your payment method is expiring soon. Please add one before it expires.');
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);
    }
}
