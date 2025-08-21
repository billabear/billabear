<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Tax;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use BillaBear\Entity\Country;
use BillaBear\Entity\TaxType;
use BillaBear\Repository\Orm\CountryRepository;
use BillaBear\Repository\Orm\TaxTypeRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class TaxTypeContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private TaxTypeRepository $taxTypeRepository,
        private CountryRepository $countryRepository,
    ) {
    }

    /**
     * @When I create a tax type with the name :arg1
     */
    public function iCreateATaxTypeWithTheName($name)
    {
        $this->sendJsonRequest('POST', '/app/tax/type', ['name' => $name]);
    }

    /**
     * @When I create a tax type with:
     */
    public function iCreateATaxTypeWith(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
            'physical' => boolval($data['Physical'] ?? 'false'),
        ];

        $this->sendJsonRequest('POST', '/app/tax/type', $payload);
    }

    /**
     * @Then there will be a tax type with the name :arg1
     */
    public function thereWillBeATaxTypeWithTheName($name)
    {
        $taxType = $this->taxTypeRepository->findOneBy(['name' => $name]);

        if (!$taxType instanceof TaxType) {
            throw new \Exception('Tax type not found');
        }
    }

    /**
     * @Given there are the following tax types:
     */
    public function thereAreTheFollowingTaxTypes(TableNode $table)
    {
        $data = $table->getColumnsHash();
        /** @var Country[] $countries */
        $countries = $this->countryRepository->findAll();
        foreach ($data as $row) {
            $taxType = new TaxType();
            $taxType->setName($row['Name']);
            $this->taxTypeRepository->getEntityManager()->persist($taxType);
        }

        $this->taxTypeRepository->getEntityManager()->flush();
    }

    /**
     * @When I make the tax type :arg1 default
     */
    public function iMakeTheTaxTypeDefault($taxName)
    {
        $taxType = $this->getTaxTypeByName($taxName);

        $this->sendJsonRequest('POST', '/app/tax/type/'.$taxType->getId().'/default');
    }

    /**
     * @Then the tax type :arg1 is default
     */
    public function theTaxTypeIsDefault($taxName)
    {
        $taxType = $this->getTaxTypeByName($taxName);

        if (!$taxType->isDefault()) {
            throw new \Exception('Tax type is not default');
        }
    }

    /**
     * @Then the tax type :arg1 is not default
     */
    public function theTaxTypeIsNotDefault($taxName)
    {
        $taxType = $this->getTaxTypeByName($taxName);

        if ($taxType->isDefault()) {
            throw new \Exception('Tax type is default');
        }
    }

    /**
     * @When I go to the tax types list
     */
    public function iGoToTheTaxTypesList()
    {
        $this->sendJsonRequest('GET', '/app/tax/type');
    }

    /**
     * @Then I will see a tax type in the list called :arg1
     */
    public function iWillSeeATaxTypeInTheListCalled($name)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $item) {
            if ($item['name'] === $name) {
                return;
            }
        }

        throw new \Exception(sprintf("Can't see tax type '%s'", $name));
    }

    /**
     * @Then I will see a tax type in the tax type dropdown called :arg1
     */
    public function iWillSeeATaxTypeInTheTaxTypeDropdownCalled($name)
    {
        $data = $this->getJsonContent();
        foreach ($data['tax_types'] as $item) {
            if ($item['name'] === $name) {
                return;
            }
        }

        throw new \Exception(sprintf("Can't see tax type '%s'", $name));
    }

    protected function getTaxTypeByName(string $name): TaxType
    {
        $taxType = $this->taxTypeRepository->findOneBy(['name' => $name]);

        if (!$taxType instanceof TaxType) {
            throw new \Exception('Tax type not found');
        }
        $this->taxTypeRepository->getEntityManager()->refresh($taxType);

        return $taxType;
    }
}
