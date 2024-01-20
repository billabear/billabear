<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Tax;

use App\Entity\Country;
use App\Entity\CountryTaxRule;
use App\Entity\TaxType;
use App\Repository\Orm\CountryRepository;
use App\Repository\Orm\CountryTaxRuleRepository;
use App\Repository\Orm\TaxTypeRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class CountryContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private CountryRepository $countryRepository,
        private TaxTypeRepository $taxTypeRepository,
        private CountryTaxRuleRepository $countryTaxRuleRepository,
    ) {
    }

    public function getTaxType(string $name): TaxType
    {
        $taxType = $this->taxTypeRepository->findOneBy(['name' => $name]);

        if (!$taxType instanceof TaxType) {
            throw new \Exception(sprintf("No tax type called '%s' found", $name));
        }

        return $taxType;
    }

    /**
     * @When I create a country with the following data:
     */
    public function iCreateACountryWithTheFollowingData(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'] ?? null,
            'iso_code' => $data['ISO Code'] ?? null,
            'threshold' => intval($data['Threshold'] ?? 0),
            'currency' => $data['Currency'],
        ];

        $this->sendJsonRequest('POST', '/app/country', $payload);
    }

    /**
     * @When I the edit country for :arg1 with:
     */
    public function iTheEditCountryForWith($name, TableNode $table)
    {
        $country = $this->getCountryByName($name);

        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'] ?? null,
            'iso_code' => $data['ISO Code'] ?? null,
            'threshold' => intval($data['Threshold'] ?? 0),
            'currency' => $data['Currency'],
        ];

        $this->sendJsonRequest('POST', '/app/country/'.$country->getId().'/edit', $payload);
    }

    /**
     * @Then there will be a country called :arg1 with the threshold :arg2
     */
    public function thereWillBeACountryCalledWithTheThreshold($name, $threshold)
    {
        $country = $this->getCountryByName($name);

        if ($country->getThreshold() !== intval($threshold)) {
            throw new \Exception(sprintf('Got %d but expected %d', $country->getThreshold(), $threshold));
        }
    }

    /**
     * @Then there will be a country called :arg1 with the ISO Code :arg2
     */
    public function thereWillBeACountryCalledWithTheIsoCode($arg1, $arg2)
    {
        $country = $this->countryRepository->findOneBy(['name' => $arg1, 'isoCode' => $arg2]);

        if (!$country instanceof Country) {
            var_dump($this->getJsonContent());
            throw new \Exception('No Country found');
        }
    }

    /**
     * @Given that the following countries exist:
     */
    public function thatTheFollowingCountriesExist(TableNode $table)
    {
        $data = $table->getColumnsHash();
        foreach ($data as $row) {
            $country = new Country();
            $country->setName($row['Name']);
            $country->setIsoCode($row['ISO Code']);
            $country->setCurrency($row['Currency']);
            $country->setThreshold(intval($row['Threshold']));
            $country->setCreatedAt(new \DateTime());
            $country->setEnabled($row['Enabled'] ?? true);

            $this->countryRepository->getEntityManager()->persist($country);
        }
        $this->countryRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the countries list
     */
    public function iViewTheCountriesList()
    {
        $this->sendJsonRequest('GET', '/app/countries');
    }

    /**
     * @Then I will see the country :arg1 in the list
     */
    public function iWillSeeTheCountryInTheList($name)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $country) {
            if ($country['name'] === $name) {
                return;
            }
        }

        throw new \Exception('Not found');
    }

    /**
     * @When I goto the edit country for :arg1
     */
    public function iGotoTheEditCountryFor($name)
    {
        $country = $this->getCountryByName($name);
        $this->sendJsonRequest('GET', '/app/country/'.$country->getId().'/view');
    }

    /**
     * @Then I will see that there is a threshold for the country of :arg1
     */
    public function iWillSeeThatThereIsAThresholdForTheCountryOf($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['country']['threshold'] !== intval($arg1)) {
            throw new \Exception("Can't see threshold");
        }
    }

    /**
     * @Then I will see the currency is :arg1
     */
    public function iWillSeeTheCurrencyIs($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['country']['currency'] !== $arg1) {
            throw new \Exception("Can't see currency");
        }
    }

    /**
     * @Then I will see the ISO code is :arg1
     */
    public function iWillSeeTheIsoCodeIs($arg1)
    {
        $data = $this->getJsonContent();

        if ($data['country']['iso_code'] !== $arg1) {
            throw new \Exception("Can't see ISO code");
        }
    }

    protected function getCountryByName(string $name): Country
    {
        $country = $this->countryRepository->findOneBy(['name' => $name]);

        if (!$country) {
            throw new \Exception("Can't find country");
        }
        $this->countryRepository->getEntityManager()->refresh($country);

        return $country;
    }

    /**
     * @When I create a country tax rule with the following data:
     */
    public function iCreateACountryTaxRuleWithTheFollowingData(TableNode $table)
    {
        $data = $table->getRowsHash();

        $country = $this->getCountryByName($data['Country']);
        $taxType = $this->getTaxType($data['Tax Type']);
        $validFrom = new \DateTime($data['Valid From']);

        $payload = [
            'tax_type' => (string) $taxType->getId(),
            'tax_rate' => floatval($data['Tax Rate']),
            'valid_from' => $validFrom->format(\DATE_RFC3339_EXTENDED),
            'default' => boolval($data['Default'] ?? 'true'),
        ];

        $this->sendJsonRequest('POST', '/app/country/'.$country->getId().'/tax-rule', $payload);
    }

    /**
     * @Then there should be a tax rule for :arg1 for :arg2 tax type with the tax rate :arg3
     */
    public function thereShouldBeATaxRuleForForTaxTypeWithTheTaxRate($country, $taxType, $taxRate)
    {
        $country = $this->getCountryByName($country);
        $taxType = $this->getTaxType($taxType);

        $countryTaxRule = $this->countryTaxRuleRepository->findOneBy(['country' => $country, 'taxType' => $taxType]);

        if (!$countryTaxRule instanceof CountryTaxRule) {
            var_dump($this->getJsonContent());
            throw new \Exception('No tax rule found');
        }

        if (floatval($taxRate) !== $countryTaxRule->getTaxRate()) {
            throw new \Exception('Got %f instead of %f', $taxRate, $countryTaxRule->getTaxRate());
        }
    }
}
