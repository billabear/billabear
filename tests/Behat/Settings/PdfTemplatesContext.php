<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Settings;

use App\Entity\Template;
use App\Repository\Orm\TemplateRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class PdfTemplatesContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session, private TemplateRepository $templateRepository)
    {
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
