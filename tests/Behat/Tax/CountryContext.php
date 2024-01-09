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
use App\Repository\Orm\CountryRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class CountryContext implements Context
{
    use SendRequestTrait;

    public function __construct(private Session $session, private CountryRepository $countryRepository)
    {
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
        ];

        $this->sendJsonRequest('POST', '/app/country', $payload);
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
            $country = new Country();
            $country->setName($row['Name']);
            $country->setIsoCode($row['ISO Code']);
            $country->setCreatedAt(new \DateTime());

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
}
