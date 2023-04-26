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

use App\Entity\BrandSettings;
use App\Repository\Orm\BrandSettingsRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class BrandsContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private BrandSettingsRepository $brandSettingsRepository,
    ) {
    }

    /**
     * @When I go to the brand list
     */
    public function iGoToTheBrandList()
    {
        $this->sendJsonRequest('GET', '/app/settings/brand');
    }

    /**
     * @Then I should see the brand :arg1
     */
    public function iShouldSeeTheBrand($brandName)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $brand) {
            if ($brand['name'] === $brandName) {
                return;
            }
        }

        throw new \Exception('Brand not found');
    }

    /**
     * @Given the follow brands exist:
     */
    public function theFollowBrandsExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $brand = new BrandSettings();
            $brand->setBrandName($row['Name']);
            $brand->setCode($row['Code']);
            $brand->setEmailAddress($row['Email']);

            $this->brandSettingsRepository->getEntityManager()->persist($brand);
        }
        $this->brandSettingsRepository->getEntityManager()->flush();
    }
}
