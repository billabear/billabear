<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Tax;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\State;
use BillaBear\Entity\StateTaxRule;
use BillaBear\Entity\TaxType;
use BillaBear\Repository\Orm\CountryRepository;
use BillaBear\Repository\Orm\CountryTaxRuleRepository;
use BillaBear\Repository\Orm\StateRepository;
use BillaBear\Repository\Orm\StateTaxRuleRepository;
use BillaBear\Repository\Orm\TaxTypeRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class CountryContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private CountryRepository $countryRepository,
        private StateRepository $stateRepository,
        private TaxTypeRepository $taxTypeRepository,
        private CountryTaxRuleRepository $countryTaxRuleRepository,
        private StateTaxRuleRepository $stateTaxRuleRepository,
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
     * @When I create a state with the following data
     */
    public function iCreateAStateWithTheFollowingData(TableNode $table)
    {
        $data = $table->getRowsHash();
        $country = $this->getCountryByName($data['Country']);
        $payload = [
            'name' => $data['Name'],
            'code' => $data['Code'],
            'threshold' => intval($data['Threshold']),
            'has_nexus' => boolval($data['Has Nexus'] ?? 'false'),
            'country' => (string) $country->getId(),
        ];

        $this->sendJsonRequest('POST', '/app/country/'.$country->getId().'/state', $payload);
    }

    /**
     * @Then there will be a state :arg1 in the country :arg2
     */
    public function thereWillBeAStateInTheCountry($stateName, $countryName)
    {
        $country = $this->getCountryByName($countryName);
        $state = $this->stateRepository->findOneBy(['name' => $stateName, 'country' => $country]);

        if (!$state instanceof State) {
            var_dump($this->getJsonContent());
            throw new \Exception("Can't find state");
        }
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
            'default' => boolval($data['Default'] ?? 'true'),
            'in_eu' => boolval($data['In EU'] ?? 'true'),
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
            'in_eu' => boolval($data['In EU'] ?? 'true'),
            'enabled' => boolval($data['Enabled'] ?? 'true'),
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
            if (!$country = $this->countryRepository->findOneBy(['isoCode' => $row['ISO Code']])) {
                $country = new Country();
            }
            $country->setName($row['Name']);
            $country->setIsoCode(trim($row['ISO Code']));
            $country->setCurrency($row['Currency']);
            $country->setThreshold(intval($row['Threshold']));
            $country->setCreatedAt(new \DateTime());
            $country->setEnabled('true' === strtolower($row['Enabled'] ?? 'true'));
            $country->setInEu('true' === strtolower($row['In EU'] ?? 'false'));
            $country->setCollecting('true' === strtolower($row['Collecting'] ?? 'false'));

            $this->countryRepository->getEntityManager()->persist($country);
        }
        $this->countryRepository->getEntityManager()->flush();
    }

    /**
     * @Given the following states exist:
     */
    public function theFollowingStatesExist(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $country = $this->getCountryByName($row['Country']);

            $state = new State();
            $state->setCountry($country);
            $state->setName($row['Name']);
            $state->setCode($row['Code']);
            $state->setThreshold(intval($row['Threshold'] ?? 0));
            $state->setHasNexus('true' === strtolower($row['Has Nexus'] ?? 'false'));
            $this->stateRepository->getEntityManager()->persist($state);
        }

        $this->stateRepository->getEntityManager()->flush();
    }

    /**
     * @Given the following state tax rules exist:
     */
    public function theFollowingStateTaxRulesExist(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $country = $this->getCountryByName($row['Country']);
            $state = $this->getStateByCountryAndName($country, $row['State']);

            $rule = new StateTaxRule();
            $rule->setState($state);
            $rule->setTaxRate(floatval($row['Tax Rate']));
            $rule->setCreatedAt(new \DateTime());
            $rule->setTaxType($this->getTaxType($row['Tax Type']));
            $rule->setValidFrom(new \DateTime($row['Valid From']));

            if (isset($row['Valid Until'])) {
                $rule->setValidUntil(new \DateTime($row['Valid Until']));
            }

            $rule->setIsDefault(boolval($row['Is Default'] ?? 'true'));
            $this->stateRepository->getEntityManager()->persist($rule);
        }
        $this->stateRepository->getEntityManager()->flush();
    }

    /**
     * @When I update the state tax rule for :arg1 and :arg2 with tax type :arg3 and tax rate :arg4 with the values:
     */
    public function iUpdateTheStateTaxRuleForAndWithTaxTypeAndTaxRateWithTheValues($countryName, $stateName, $taxType, $taxRate, TableNode $table)
    {
        $country = $this->getCountryByName($countryName);
        $state = $this->getStateByCountryAndName($country, $stateName);
        $taxType = $this->getTaxType($taxType);

        $stateTaxRule = $this->stateTaxRuleRepository->findOneBy(['state' => $state, 'taxType' => $taxType, 'taxRate' => $taxRate]);

        if (!$stateTaxRule instanceof StateTaxRule) {
            throw new \Exception("Can't find tax rule");
        }

        $data = $table->getRowsHash();
        $validFrom = new \DateTime($data['Valid From']);

        $payload = [
            'id' => (string) $stateTaxRule->getId(),
            'tax_type' => (string) $taxType->getId(),
            'tax_rate' => floatval($data['Tax Rate']),
            'valid_from' => $validFrom->format(\DATE_RFC3339_EXTENDED),
            'default' => boolval($data['Default'] ?? 'true'),
            'country' => (string) $country->getId(),
            'state' => (string) $state->getId(),
        ];

        if (isset($data['Valid Until'])) {
            $validUntil = new \DateTime($data['Valid Until']);
            $payload['valid_until'] = $validUntil->format(\DATE_RFC3339_EXTENDED);
        }

        $this->sendJsonRequest('POST', sprintf('/app/country/%s/state/%s/tax-rule/%s/edit', $country->getId(), $state->getId(), $stateTaxRule->getId()), $payload);
    }

    /**
     * @Then there should be a tax rule for :arg1 and :arg2 for :arg3 tax type with the tax rate :arg4
     */
    public function thereShouldBeATaxRuleForAndForTaxTypeWithTheTaxRate($countryName, $stateName, $taxType, $taxRate)
    {
        $country = $this->getCountryByName($countryName);
        $state = $this->getStateByCountryAndName($country, $stateName);
        $taxType = $this->getTaxType($taxType);

        $stateTaxRule = $this->stateTaxRuleRepository->findOneBy(['state' => $state, 'taxType' => $taxType, 'taxRate' => $taxRate]);

        if (!$stateTaxRule instanceof StateTaxRule) {
            var_dump($this->getJsonContent());
            throw new \Exception("Can't find tax rule");
        }
    }

    /**
     * @Then there should not be a tax rule for :arg1 and :arg2 for :arg3 tax type with the tax rate :arg4
     */
    public function thereShouldNotBeATaxRuleForAndForTaxTypeWithTheTaxRate($countryName, $stateName, $taxType, $taxRate)
    {
        $country = $this->getCountryByName($countryName);
        $state = $this->getStateByCountryAndName($country, $stateName);
        $taxType = $this->getTaxType($taxType);

        $stateTaxRule = $this->stateTaxRuleRepository->findOneBy(['state' => $state, 'taxType' => $taxType, 'taxRate' => $taxRate]);

        if ($stateTaxRule instanceof StateTaxRule) {
            throw new \Exception('Found find tax rule');
        }
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
     * @When I view the country for :arg1
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

    protected function getStateByCountryAndName(Country $country, string $name): State
    {
        $state = $this->stateRepository->findOneBy(['country' => $country, 'name' => $name]);

        if (!$state) {
            throw new \Exception("Can't find state");
        }
        $this->stateRepository->getEntityManager()->refresh($state);

        return $state;
    }

    /**
     * @When I create a state tax rule with the following data:
     */
    public function iCreateAStateTaxRuleWithTheFollowingData(TableNode $table)
    {
        $data = $table->getRowsHash();

        $country = $this->getCountryByName($data['Country']);
        $state = $this->getStateByCountryAndName($country, $data['State']);
        $taxType = $this->getTaxType($data['Tax Type']);
        $validFrom = new \DateTime($data['Valid From']);

        $payload = [
            'tax_type' => (string) $taxType->getId(),
            'tax_rate' => floatval($data['Tax Rate']),
            'valid_from' => $validFrom->format(\DATE_RFC3339_EXTENDED),
            'default' => boolval($data['Default'] ?? 'true'),
            'country' => (string) $country->getId(),
            'state' => (string) $state->getId(),
        ];

        if (isset($data['Valid Until'])) {
            $validUntil = new \DateTime($data['Valid Until']);
            $payload['valid_until'] = $validUntil->format(\DATE_RFC3339_EXTENDED);
        }

        $this->sendJsonRequest('POST', '/app/country/'.$country->getId().'/state/'.$state->getId().'/tax-rule', $payload);
    }

    /**
     * @Then there should be a state tax rule for :arg1 and :arg2 for :arg3 tax type with the tax rate :arg4
     */
    public function thereShouldBeAStateTaxRuleForAndForTaxTypeWithTheTaxRate($countryName, $stateName, $taxType, $rate)
    {
        $country = $this->getCountryByName($countryName);
        $state = $this->getStateByCountryAndName($country, $stateName);
        $taxType = $this->getTaxType($taxType);

        $stateTaxRule = $this->stateTaxRuleRepository->findOneBy(['state' => $state, 'taxType' => $taxType, 'taxRate' => $rate]);

        if (!$stateTaxRule instanceof StateTaxRule) {
            throw new \Exception("Can't find state tax rule");
        }
    }

    /**
     * @Then there should be a tax rule for :arg1 and :arg2 for :arg3 tax type with the tax rate :arg5 that is valid until :arg4
     */
    public function thereShouldBeATaxRuleForAndForTaxTypeWithTheTaxRateThatIsValidUntil($countryName, $stateName, $taxType, $rate, $validUntilStr)
    {
        $country = $this->getCountryByName($countryName);
        $state = $this->getStateByCountryAndName($country, $stateName);
        $taxType = $this->getTaxType($taxType);

        $stateTaxRule = $this->stateTaxRuleRepository->findOneBy(['state' => $state, 'taxType' => $taxType, 'taxRate' => $rate]);

        if (!$stateTaxRule instanceof StateTaxRule) {
            throw new \Exception("Can't find state tax rule");
        }
        $this->stateRepository->getEntityManager()->refresh($stateTaxRule);
        $validUntil = new \DateTime($validUntilStr);
        if ($validUntil->format('Y-m-d') !== $stateTaxRule->getValidUntil()?->format('Y-m-d')) {
            throw new \Exception(sprintf('Wrong date - expected %s but got %s', $validUntil->format('Y-m-d'), $stateTaxRule->getValidUntil()?->format('Y-m-d')));
        }
    }

    /**
     * @Then there should be a tax rule for :arg1 and :arg2 for :arg3 tax type with the tax rate :arg4 that is open ended
     */
    public function thereShouldBeATaxRuleForAndForTaxTypeWithTheTaxRateThatIsOpenEnded($countryName, $stateName, $taxType, $rate)
    {
        $country = $this->getCountryByName($countryName);
        $state = $this->getStateByCountryAndName($country, $stateName);
        $taxType = $this->getTaxType($taxType);

        $stateTaxRule = $this->stateTaxRuleRepository->findOneBy(['state' => $state, 'taxType' => $taxType, 'taxRate' => $rate, 'validUntil' => null]);

        if (!$stateTaxRule instanceof StateTaxRule) {
            throw new \Exception("Can't find state tax rule");
        }
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
            'country' => (string) $country->getId(),
            'in_eu' => boolval($data['In EU'] ?? 'true'),
        ];

        if (isset($data['Valid Until'])) {
            $validUntil = new \DateTime($data['Valid Until']);
            $payload['valid_until'] = $validUntil->format(\DATE_RFC3339_EXTENDED);
        }

        $this->sendJsonRequest('POST', '/app/country/'.$country->getId().'/tax-rule', $payload);
    }

    /**
     * @Given the following country tax rules exist:
     */
    public function theFollowingCountryTaxRulesExist(TableNode $table)
    {
        $data = $table->getColumnsHash();

        foreach ($data as $row) {
            $country = $this->getCountryByName($row['Country']);
            $taxType = $this->getTaxType($row['Tax Type']);
            $validFrom = new \DateTime($row['Valid From'] ?? '-100 days');

            $countryTaxRule = new CountryTaxRule();
            $countryTaxRule->setCountry($country);
            $countryTaxRule->setTaxType($taxType);
            $countryTaxRule->setTaxRate(floatval($row['Tax Rate']));
            $countryTaxRule->setValidFrom($validFrom);
            $countryTaxRule->setIsDefault(boolval($row['Default'] ?? 'true'));
            $countryTaxRule->setCreatedAt(new \DateTime());

            if (isset($row['Valid Until'])) {
                $countryTaxRule->setValidUntil(new \DateTime($row['Valid Until']));
            }

            $this->countryTaxRuleRepository->getEntityManager()->persist($countryTaxRule);
        }

        $this->countryTaxRuleRepository->getEntityManager()->flush();
    }

    /**
     * @Then I should see the tax rule for tax type :arg1 with the tax rate :arg2
     */
    public function iShouldSeeTheTaxRuleForTaxTypeWithTheTaxRate($arg1, $arg2)
    {
        $data = $this->getJsonContent();

        foreach ($data['country_tax_rules'] as $taxRule) {
            if ($taxRule['tax_type']['name'] !== $arg1) {
                continue;
            }
            if ($taxRule['tax_rate'] == floatval($arg2)) {
                return;
            }
        }

        throw new \Exception("Can't find tax rule");
    }

    /**
     * @Then I should see the state :arg1 in the list of states
     */
    public function iShouldSeeTheStateInTheListOfStates($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['states'] as $state) {
            if ($state['name'] === $arg1) {
                return;
            }
        }

        throw new \Exception("Can't find state");
    }

    /**
     * @When I update the country tax rule for :arg1 with tax type :arg2 and tax rate :arg3 with the values:
     */
    public function iUpdateTheCountryTaxRuleForWithTaxTypeAndTaxRateWithTheValues($country, $taxType, $taxRate, TableNode $table)
    {
        $country = $this->getCountryByName($country);
        $taxType = $this->getTaxType($taxType);

        $countryTaxRule = $this->countryTaxRuleRepository->findOneBy(['country' => $country, 'taxType' => $taxType, 'taxRate' => floatval($taxRate)]);
        if (!$countryTaxRule instanceof CountryTaxRule) {
            throw new \Exception('No tax rule found');
        }

        $data = $table->getRowsHash();

        $country = $this->getCountryByName($data['Country']);
        $taxType = $this->getTaxType($data['Tax Type']);
        $validFrom = new \DateTime($data['Valid From']);

        $payload = [
            'id' => (string) $countryTaxRule->getId(),
            'tax_type' => (string) $taxType->getId(),
            'tax_rate' => floatval($data['Tax Rate']),
            'valid_from' => $validFrom->format(\DATE_RFC3339_EXTENDED),
            'default' => boolval($data['Default'] ?? 'true'),
            'country' => (string) $country->getId(),
        ];

        if (isset($data['Valid Until'])) {
            $validUntil = new \DateTime($data['Valid Until']);
            $payload['valid_until'] = $validUntil->format(\DATE_RFC3339_EXTENDED);
        }

        $this->sendJsonRequest('POST', '/app/country/'.$country->getId().'/tax-rule/'.$countryTaxRule->getId().'/edit', $payload);
    }

    /**
     * @Then there should not be a tax rule for :arg1 for :arg2 tax type with the tax rate :arg3
     */
    public function thereShouldNotBeATaxRuleForForTaxTypeWithTheTaxRate($country, $taxType, $taxRate)
    {
        $country = $this->getCountryByName($country);
        $taxType = $this->getTaxType($taxType);

        $countryTaxRule = $this->countryTaxRuleRepository->findOneBy(['country' => $country, 'taxType' => $taxType, 'taxRate' => floatval($taxRate)]);

        if ($countryTaxRule instanceof CountryTaxRule) {
            var_dump($this->getJsonContent());
            throw new \Exception('tax rule found');
        }
    }

    /**
     * @Then there should be a tax rule for :arg1 for :arg2 tax type with the tax rate :arg3
     */
    public function thereShouldBeATaxRuleForForTaxTypeWithTheTaxRate($country, $taxType, $taxRate)
    {
        $country = $this->getCountryByName($country);
        $taxType = $this->getTaxType($taxType);

        $countryTaxRule = $this->countryTaxRuleRepository->findOneBy(['country' => $country, 'taxType' => $taxType, 'taxRate' => $taxRate]);

        if (!$countryTaxRule instanceof CountryTaxRule) {
            var_dump($this->getJsonContent());
            throw new \Exception('No tax rule found');
        }
        $this->countryTaxRuleRepository->getEntityManager()->refresh($countryTaxRule);

        if (floatval($taxRate) !== $countryTaxRule->getTaxRate()) {
            throw new \Exception(sprintf('Got %f instead of %f', $taxRate, $countryTaxRule->getTaxRate()));
        }
    }

    /**
     * @Then there should be a tax rule for :arg1 for :arg2 tax type with the tax rate :arg4 that is valid until :arg3
     */
    public function thereShouldBeATaxRuleForForTaxTypeWithTheTaxRateThatIsValidUntil($country, $taxType, $taxRate, $validUntilStr)
    {
        $country = $this->getCountryByName($country);
        $taxType = $this->getTaxType($taxType);

        $countryTaxRule = $this->countryTaxRuleRepository->findOneBy(['country' => $country, 'taxType' => $taxType, 'taxRate' => floatval($taxRate)]);

        if ($countryTaxRule->getTaxRate() != $taxRate) {
            throw new \Exception('Wrong tax rate');
        }

        if (!$countryTaxRule instanceof CountryTaxRule) {
            throw new \Exception('No tax rule found');
        }
        $this->countryTaxRuleRepository->getEntityManager()->refresh($countryTaxRule);

        $validUntil = new \DateTime($validUntilStr);
        if ($validUntil->format('Y-m-d') !== $countryTaxRule->getValidUntil()?->format('Y-m-d')) {
            throw new \Exception(sprintf('Wrong date - expected %s but got %s', $validUntil->format('Y-m-d'), $countryTaxRule->getValidUntil()?->format('Y-m-d')));
        }
    }

    /**
     * @Then there should be a tax rule for :arg1 for :arg2 tax type with the tax rate :arg3 that is open ended
     */
    public function thereShouldBeATaxRuleForForTaxTypeWithTheTaxRateThatIsOpenEnded($country, $taxType, $taxRate)
    {
        $country = $this->getCountryByName($country);
        $taxType = $this->getTaxType($taxType);

        $countryTaxRule = $this->countryTaxRuleRepository->findOneBy(['country' => $country, 'taxType' => $taxType, 'taxRate' => $taxRate, 'validUntil' => null]);

        if (!$countryTaxRule instanceof CountryTaxRule) {
            var_dump($this->getJsonContent());
            throw new \Exception('No tax rule found');
        }
        $this->countryTaxRuleRepository->getEntityManager()->refresh($countryTaxRule);
    }
}
