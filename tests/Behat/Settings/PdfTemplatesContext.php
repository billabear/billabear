<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Settings;

use App\Entity\Settings;
use App\Entity\Template;
use App\Enum\PdfGeneratorType;
use App\Repository\Orm\SettingsRepository;
use App\Repository\Orm\TemplateRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class PdfTemplatesContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private TemplateRepository $templateRepository,
        private SettingsRepository $settingsRepository,
    ) {
    }

    /**
     * @When I set the pdf generator to:
     */
    public function iSetThePdfGeneratorTo(TableNode $table)
    {
        $data = $table->getRowsHash();

        $payload = [
          'generator' => $data['Generator'],
        ];

        if (isset($data['Tmp Dir'])) {
            $payload['tmp_dir'] = $data['Tmp Dir'];
        }

        if (isset($data['Api Key'])) {
            $payload['api_key'] = $data['Api Key'];
        }

        if (isset($data['Bin'])) {
            $payload['bin'] = $data['Bin'];
        }

        $this->sendJsonRequest('POST', '/app/settings/pdf-generator', $payload);
    }

    /**
     * @Then the pdf generator should be :arg1
     */
    public function thePdfGeneratorShouldBe($generator)
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->findOneBy([]);
        $this->settingsRepository->getEntityManager()->refresh($settings);

        $type = PdfGeneratorType::fromName($generator);
        if ($settings->getSystemSettings()->getPdfGenerator() != $type) {
            throw new \Exception(sprintf("expected '%s' but got '%s'", $generator, $settings->getSystemSettings()->getPdfGenerator()?->value));
        }
    }

    /**
     * @Then the pdf generator api key should be :arg1
     */
    public function thePdfGeneratorApiKeyShouldBe($apiKey)
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->findOneBy([]);
        $this->settingsRepository->getEntityManager()->refresh($settings);

        if ($settings->getSystemSettings()->getPdfApiKey() != $apiKey) {
            throw new \Exception(sprintf("expected '%s' but got '%s'", $apiKey, $settings->getSystemSettings()->getPdfTmpDir()));
        }
    }

    /**
     * @Then the pdf generator bin should be :arg1
     */
    public function thePdfGeneratorBinShouldBe($bin)
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->findOneBy([]);
        $this->settingsRepository->getEntityManager()->refresh($settings);

        if ($settings->getSystemSettings()->getPdfBin() != $bin) {
            throw new \Exception(sprintf("expected '%s' but got '%s'", $bin, $settings->getSystemSettings()->getPdfTmpDir()));
        }
    }

    /**
     * @Then the pdf generator tmp dir should be :arg1
     */
    public function thePdfGeneratorTmpDirShouldBe($tmpDir)
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->findOneBy([]);
        $this->settingsRepository->getEntityManager()->refresh($settings);

        if ($settings->getSystemSettings()->getPdfTmpDir() != $tmpDir) {
            throw new \Exception(sprintf("expected '%s' but got '%s'", $tmpDir, $settings->getSystemSettings()->getPdfTmpDir()));
        }
    }

    /**
     * @Given the following pdf templates exist:
     */
    public function theFollowingPdfTemplatesExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $template = new Template();
            $template->setName($row['Name']);
            $template->setBrand($row['Brand'] ?? 'default');
            $template->setContent($row['Content']);
            $this->templateRepository->getEntityManager()->persist($template);
        }
        $this->templateRepository->getEntityManager()->flush();
    }

    /**
     * @When I go to the pdf templates
     */
    public function iGoToThePdfTemplates()
    {
        $this->sendJsonRequest('GET', '/app/settings/template');
    }

    /**
     * @Then I will see the pdf template for :arg1
     */
    public function iWillSeeThePdfTemplateFor($templateName)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $template) {
            if ($template['name'] === $templateName) {
                return;
            }
        }

        throw new \Exception("Can't find template");
    }

    /**
     * @When I go to the pdf template for :arg1 in brand :arg2
     */
    public function iGoToThePdfTemplateForInGroup($templateName, $customerGroup)
    {
        $template = $this->getTemplate($templateName, $customerGroup);

        $this->sendJsonRequest('GET', '/app/settings/template/'.$template->getId());
    }

    /**
     * @Then I will see the the template content of :arg1
     */
    public function iWillSeeTheTheTemplateContentOf($contentBody)
    {
        $data = $this->getJsonContent();

        if ($data['content'] !== $contentBody) {
            throw new \Exception('Wrong content');
        }
    }

    protected function getTemplate(string $templateName, string $customerGroup): Template
    {
        $template = $this->templateRepository->findOneBy(['name' => $templateName, 'brand' => $customerGroup]);

        if (!$template instanceof Template) {
            throw new \Exception("Can't find template");
        }

        $this->templateRepository->getEntityManager()->refresh($template);

        return $template;
    }

    /**
     * @When I update the pdf template for :arg1 in brand :arg2 with:
     */
    public function iUpdateThePdfTemplateForInGroupWith($templateName, $customerGroup, TableNode $table)
    {
        $template = $this->getTemplate($templateName, $customerGroup);

        $this->sendJsonRequest('POST', '/app/settings/template/'.$template->getId(), ['content' => $table->getRowsHash()['Content']]);
    }

    /**
     * @Then the pdf template for :arg1 in brand :arg2 will have the content :arg3
     */
    public function thePdfTemplateForInGroupWillHaveTheContent($templateName, $customerGroup, $contentBody)
    {
        $template = $this->getTemplate($templateName, $customerGroup);
        if ($template->getContent() !== $contentBody) {
            throw new \Exception('Wrong content');
        }
    }
}
