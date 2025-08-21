<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Email;

use BillaBear\Entity\Customer;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Repository\EmailTemplateRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class EmailTemplateProvider
{
    use LoggerAwareTrait;

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

        $this->getLogger()->warning('Unable to find email template', ['template_name' => $templateName]);

        throw new \Exception('Unable to find email template');
    }
}
