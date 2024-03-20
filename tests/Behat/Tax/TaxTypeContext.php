<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Tax;

use App\Entity\TaxType;
use App\Repository\Orm\TaxTypeRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class TaxTypeContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private TaxTypeRepository $taxTypeRepository,
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
        foreach ($data as $row) {
            $taxType = new TaxType();
            $taxType->setName($row['Name']);
            $taxType->setPhysical('true' === strtolower($row['Physical'] ?? 'false'));
            $this->taxTypeRepository->getEntityManager()->persist($taxType);
        }

        $this->taxTypeRepository->getEntityManager()->flush();
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
}
