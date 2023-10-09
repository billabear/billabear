<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: xx.10.2026 ( 3 years after 2023.4 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
