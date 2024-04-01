<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Settings;

use App\Entity\Customer;
use App\Entity\EmailTemplate;
use App\Repository\Orm\BrandSettingsRepository;
use App\Repository\Orm\EmailTemplateRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class EmailTemplatesContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private EmailTemplateRepository $templateRepository,
        private BrandSettingsRepository $brandSettingsRepository,
    ) {
    }

    /**
     * @When I create an email template:
     */
    public function iCreateAnEmailTemplate(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
            'locale' => $data['Locale'],
            'subject' => $data['Subject'] ?? null,
            'template_body' => $data['Template Body'] ?? null,
            'template_id' => $data['Template ID'] ?? null,
            'use_emsp_template' => ('true' === strtolower($data['Use Emsp Template'] ?? 'false')),
            'brand' => $data['Brand'] ?? Customer::DEFAULT_BRAND,
        ];

        $this->sendJsonRequest('POST', '/app/settings/email-template', $payload);
    }

    /**
     * @Then there will be an email template for :arg1 with locale :arg2
     */
    public function thereWillBeAnEmailTemplateForWithLocale($templateName, $locale)
    {
        $this->getEmailTemplate($templateName, $locale);
    }

    /**
     * @Then there will not be an email template for :arg1 with locale :arg2
     */
    public function thereWillNotBeAnEmailTemplateForWithLocale($templateName, $locale)
    {
        try {
            $this->getEmailTemplate($templateName, $locale);
        } catch (\Throwable $exception) {
            return;
        }
        throw new \Exception('Found');
    }

    public function getEmailTemplate($templateName, $locale): EmailTemplate
    {
        $emailTemplate = $this->templateRepository->findOneBy(['name' => $templateName, 'locale' => $locale]);

        if (!$emailTemplate instanceof EmailTemplate) {
            throw new \Exception('No template found');
        }

        $this->templateRepository->getEntityManager()->refresh($emailTemplate);

        return $emailTemplate;
    }

    /**
     * @Given the following email templates exist:
     */
    public function theFollowingEmailTemplatesExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $brandSettings = $this->brandSettingsRepository->findOneBy(['code' => $row['Brand'] ?? Customer::DEFAULT_BRAND]);

            $emailTemplate = new EmailTemplate();
            $emailTemplate->setName($row['Name']);
            $emailTemplate->setLocale($row['Locale']);
            $emailTemplate->setUseEmspTemplate('true' === strtolower($row['Use Emsp Template'] ?? 'false'));
            $emailTemplate->setTemplateBody($row['Template Body'] ?? null);
            $emailTemplate->setTemplateId($row['Template ID'] ?? null);
            $emailTemplate->setSubject($row['Subject'] ?? null);
            $emailTemplate->setBrand($brandSettings);

            $this->templateRepository->getEntityManager()->persist($emailTemplate);
        }
        $this->templateRepository->getEntityManager()->flush();
    }

    /**
     * @When I go to the email template list
     */
    public function iGoToTheEmailTemplateList()
    {
        $this->sendJsonRequest('GET', '/app/settings/email-template');
    }

    /**
     * @Then I will see in the list of email templates one for :arg1 with the locale :arg2
     */
    public function iWillSeeInTheListOfEmailTemplatesOneForWithTheLocale($arg1, $arg2)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $template) {
            if ($template['name'] === $arg1 && $template['locale'] === $arg2) {
                return;
            }
        }

        throw new \Exception('No template found');
    }

    /**
     * @Then I will not see in the list of email templates one for :arg1 with the locale :arg2
     */
    public function iWillNotSeeInTheListOfEmailTemplatesOneForWithTheLocale($arg1, $arg2)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $template) {
            if ($template['name'] === $arg1 && $template['locale'] === $arg2) {
                throw new \Exception('template found');
            }
        }
    }

    /**
     * @When I go to the email template for :arg1 with the locale :arg2
     */
    public function iGoToTheEmailTemplateForWithTheLocale($templateName, $locale)
    {
        $emailTemplate = $this->getEmailTemplate($templateName, $locale);
        $this->sendJsonRequest('GET', '/app/settings/email-template/'.$emailTemplate->getId());
    }

    /**
     * @Then I will see that the email template body is :arg1
     */
    public function iWillSeeThatTheEmailTemplateBodyIs($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['email_template']['template_body'] != $arg1) {
            throw new \Exception('Not the same template body');
        }
    }

    /**
     * @When I update the email template for :arg1 with the locale :arg2:
     */
    public function iUpdateTheEmailTemplateForWithTheLocale($templateName, $locale, TableNode $table)
    {
        $data = $table->getRowsHash();
        $emailTemplate = $this->getEmailTemplate($templateName, $locale);
        $payload = [
            'subject' => $data['Subject'] ?? null,
            'template_body' => $data['Template Body'] ?? null,
            'template_id' => $data['Template ID'] ?? null,
            'use_emsp_template' => ('true' === strtolower($data['Use Emsp Template'] ?? 'false')),
        ];

        $this->sendJsonRequest('POST', '/app/settings/email-template/'.$emailTemplate->getId(), $payload);
    }

    /**
     * @Then the email template for :arg1 with the locale :arg2 will have the template body is :arg3
     */
    public function theEmailTemplateForWithTheLocaleWillHaveTheTemplateBodyIs($templateName, $locale, $body)
    {
        $emailTemplate = $this->getEmailTemplate($templateName, $locale);

        if ($emailTemplate->getTemplateBody() !== $body) {
            throw new \Exception("Template body doesn't match");
        }
    }
}
