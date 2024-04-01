<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Settings;

use App\Entity\BrandSettings;
use App\Repository\Orm\BrandSettingsRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Common\Address;

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
            $brand->setAddress(new Address());
            $brand->getAddress()->setCountry($row['Country'] ?? 'DE');
            $brand->setTaxRate($row['Tax Rate'] ?? null);

            if (isset($row['Digital Services Tax Rate']) && !empty($row['Digital Services Tax Rate'])) {
                $brand->setDigitalServicesRate($row['Digital Services Tax Rate']);
            }

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
            'tax_number' => $rowsHash['Tax Number'] ?? null,
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
     * @Then the brand :arg1 should have the tax number :arg2
     */
    public function theBrandShouldHaveTheTaxNumber($brandName, $taxNumber)
    {
        $brand = $this->getBrandSettings($brandName);

        if ($brand->getTaxNumber() != $taxNumber) {
            throw new \Exception(sprintf('Expected %s got %s', $taxNumber, $brand->getTaxNumber()));
        }
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
            'tax_number' => $rowsHash['Tax Number'] ?? null,
        ];

        $this->sendJsonRequest('POST', '/app/settings/brand', $payload);
    }
}
