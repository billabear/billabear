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

    /**
     * @When I go to view the brand :arg1
     */
    public function iGoToViewTheBrand($brandName)
    {
        $brand = $this->getBrandSettings($brandName);

        $this->sendJsonRequest('GET', '/app/settings/brand/'.$brand->getId());
    }

    /**
     * @Then I should see brand data for :arg1
     */
    public function iShouldSeeBrandDataFor($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['brand']['name'] !== $arg1) {
            throw new \Exception('Not a valid brand settings');
        }
    }

    protected function getBrandSettings(string $brandName): BrandSettings
    {
        $brand = $this->brandSettingsRepository->findOneBy(['brandName' => $brandName]);

        if (!$brand instanceof BrandSettings) {
            throw new \Exception('No brand found');
        }
        $this->brandSettingsRepository->getEntityManager()->refresh($brand);

        return $brand;
    }

    /**
     * @When I go to update the brand :arg1 with:
     */
    public function iGoToUpdateTheBrandWith($brandName, TableNode $table)
    {
        $brand = $this->getBrandSettings($brandName);

        $rowsHash = $table->getRowsHash();
        $payload = [
            'name' => $rowsHash['Name'],
            'email_address' => $rowsHash['Email'],
            'address' => [
                'company_name' => $rowsHash['Company Name'],
                'street_line_one' => $rowsHash['Street Line One'],
                'city' => $rowsHash['City'],
                'region' => $rowsHash['Region'],
                'postcode' => $rowsHash['Post Code'],
                'country' => $rowsHash['Country'],
            ],
        ];

        $this->sendJsonRequest('POST', '/app/settings/brand/'.$brand->getId(), $payload);
    }

    /**
     * @Then there should be a brand with the name :arg1
     */
    public function thereShouldBeABrandWithTheName($brandName)
    {
        $this->getBrandSettings($brandName);
    }

    /**
     * @Then there should not be a brand with the name :arg1
     */
    public function thereShouldNotBeABrandWithTheName($brandName)
    {
        try {
            $this->getBrandSettings($brandName);
        } catch (\Exception $e) {
            return;
        }
        throw new \Exception('Found brand');
    }

    /**
     * @When I create a new brand
     */
    public function iCreateANewBrand(TableNode $table)
    {
        $rowsHash = $table->getRowsHash();
        $payload = [
            'code' => $rowsHash['Code'],
            'name' => $rowsHash['Name'],
            'email_address' => $rowsHash['Email'],
            'address' => [
                'company_name' => $rowsHash['Company Name'],
                'street_line_one' => $rowsHash['Street Line One'],
                'city' => $rowsHash['City'],
                'region' => $rowsHash['Region'],
                'postcode' => $rowsHash['Post Code'],
                'country' => $rowsHash['Country'],
            ],
        ];

        $this->sendJsonRequest('POST', '/app/settings/brand', $payload);
    }
}