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
            $template->setGroup($row['Group'] ?? 'default');
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
}
