<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Notification\Email;

use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Repository\EmailTemplateRepositoryInterface;

class EmailTemplateProvider
{
    public function __construct(
        private EmailTemplateRepositoryInterface $emailTemplateRepository,
    ) {
    }

    public function getTemplateForCustomer(Customer $customer, string $templateName): EmailTemplate
    {
        $emailTemplate = $this->emailTemplateRepository->getByNameAndLocaleAndBrand($templateName, $customer->getLocale(), $customer->getBrand());

        if ($emailTemplate) {
            return $emailTemplate;
        }

        $emailTemplate = $this->emailTemplateRepository->getByNameAndLocaleAndBrand($templateName, Customer::DEFAULT_LOCALE, $customer->getBrand());

        if ($emailTemplate) {
            return $emailTemplate;
        }

        $emailTemplate = $this->emailTemplateRepository->getByNameAndLocaleAndBrand($templateName, Customer::DEFAULT_LOCALE, Customer::DEFAULT_BRAND);

        if ($emailTemplate) {
            return $emailTemplate;
        }

        throw new \Exception('Unable to find email template');
    }
}
