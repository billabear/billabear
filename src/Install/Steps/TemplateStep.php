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
        $emailTemplate->setTemplateBody($this->getEmailTemplate('<p>Your subscription for plan <strong>{{ subscription.plan_name }}</strong> has been started</p>
        
        {% if subscription.has_trial %}
            <p>You have a trial which will last {{ subscription.trial_length  }} after which you will be charged {{ subscription.amount }}</p>
        {% endif %}
      
        <p>If you have any questions just reach out and we\'ll be happy to answer them!</p>'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_SUCCEEDED);
        $emailTemplate->setSubject('Payment Received');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Thanks for your payment. Here is the receipt.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_FAILED);
        $emailTemplate->setSubject('Payment Failed');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Thanks for your payment. Here is the receipt.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_FAILURE_WARNING);
        $emailTemplate->setSubject('Payment Failed');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Thanks for your payment. Here is the receipt. We\'ll try and charge you later.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_SUBSCRIPTION_CANCELLED);
        $emailTemplate->setSubject('Subscription Cancelled');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('<p>Your subscription for plan <strong>{{ subscription.plan_name }}</strong> has been cancelled</p>
        <p>You will stop being able to use the system at {{ subscription.finishes_at }}</p>
      
        <p>If you have any questions just reach out and we\'ll be happy to answer them!</p>'));
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
        $emailTemplate->setTemplateBody($this->getEmailTemplate('There are no valid payment methods attached to your account. Please add one.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setName(EmailTemplate::NAME_PAYMENT_METHOD_EXPIRY_WARNING);
        $emailTemplate->setSubject('Payment Method Expiring Soon');
        $emailTemplate->setTemplateBody($this->getEmailTemplate('Your payment method is expiring soon. Please add one before it expires.'));
        $emailTemplate->setBrand($brand);
        $emailTemplate->setUseEmspTemplate(false);
        $emailTemplate->setLocale(Customer::DEFAULT_LOCALE);
        $this->emailTemplateRepository->save($emailTemplate);
    }

    private function getEmailTemplate(string $content): string
    {
        return '<html>
    <head>
      <title></title>
    </head>
    <body style="background: rgb(254,234,0);
background: radial-gradient(circle, rgba(254,234,0,1) 0%, rgba(246,156,0,1) 100%);; color: black;">
    
    <div style="padding-top: 40px;">
      <div style="margin:auto; background-color: white; max-width: 700px; padding: 50px; border-radius: 15px; margin-top: 40px; ">
        <h1 style="text-align:center;"><img src="https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png" alt="{{ brand.name  }}" /></h1>
        
        '.$content.'
      </div>

      </div>


    </body>
  </html>';
    }
}
