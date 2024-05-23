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
use BillaBear\Entity\EconomicArea;
use BillaBear\Entity\EconomicAreaMembership;
use BillaBear\Repository\Orm\CountryRepository;
use BillaBear\Repository\Orm\EconomicAreaMembershipRepository;
use BillaBear\Repository\Orm\EconomicAreaRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class EconomicAreaContext implements Context
{
    use SendRequestTrait;
    use CountryTrait;

    public function __construct(
        private Session $session,
        private CountryRepository $countryRepository,
        private EconomicAreaRepository $economicAreaRepository,
        private EconomicAreaMembershipRepository $economicAreaMembershipRepository,
    ) {
    }

    /**
     * @When I create an economic area with the following data:
     */
    public function iCreateAnEconomicAreaWithTheFollowingData(TableNode $table)
    {
        $data = $table->getRowsHash();

        $payload = [
            'name' => $data['Name'],
            'currency' => $data['Currency'],
            'threshold' => (int) $data['Threshold'],
        ];

        $this->sendJsonRequest('POST', '/app/economic-area', $payload);
    }

    /**
     * @Then there will be an economic area called :arg1
     */
    public function thereWillBeAnEconomicAreaCalled($name)
    {
        return $this->getEconomicAreaByName($name);
    }

    /**
     * @Given that the following economic areas exist:
     */
    public function thatTheFollowingEconomicAreasExist(TableNode $table)
    {
        $data = $table->getColumnsHash();
        foreach ($data as $row) {
            $economicArea = new EconomicArea();
            $economicArea->setName($row['Name']);
            $economicArea->setEnabled(boolval($row['Enabled'] ?? 'true'));
            $economicArea->setCurrency($row['Currency']);
            $economicArea->setThreshold(intval($row['Threshold'] ?? 0));
            $economicArea->setCreatedAt(new \DateTime());

            $this->economicAreaRepository->getEntityManager()->persist($economicArea);
        }
        $this->economicAreaRepository->getEntityManager()->flush();
    }

    /**
     * @When I go to the economic areas list
     */
    public function iGoToTheEconomicAreasList()
    {
        $this->sendJsonRequest('GET', '/app/economic-areas');
    }

    /**
     * @Then I should see an economic area called :arg1
     */
    public function iShouldSeeAnEconomicAreaCalled($name)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $row) {
            if ($row['name'] === $name) {
                return;
            }
        }

        throw new \Exception('Economic Area not found');
    }

    /**
     * @When I create an economic area membership with the following data:
     */
    public function iCreateAnEconomicAreaMembershipWithTheFollowingData(TableNode $table)
    {
        $data = $table->getRowsHash();
        $country = $this->getCountryByName($data['Country']);
        $economicArea = $this->getEconomicAreaByName($data['Economic Area']);
        $date = new \DateTime($data['Joined At']);
        $payload = [
            'economic_area' => (string) $economicArea->getId(),
            'country' => (string) $country->getId(),
            'joined_at' => $date->format(\DATE_RFC3339_EXTENDED),
        ];
        $this->sendJsonRequest('POST', '/app/economic-area/member', $payload);
    }

    /**
     * @Then the country :arg1 is a member of :arg2
     */
    public function theCountryIsAMemberOf($countryName, $economicAreaName)
    {
        $country = $this->getCountryByName($countryName);
        $economicArea = $this->getEconomicAreaByName($economicAreaName);

        $ids = $economicArea->getMembers()->map(function (EconomicAreaMembership $membership) {return (string) $membership->getCountry()->getId(); })->toArray();

        if (!in_array((string) $country->getId(), $ids)) {
            var_dump($this->getJsonContent());
            throw new \Exception('Invalid');
        }
    }

    /**
     * @Then the country :arg1 is not a member of :arg2
     */
    public function theCountryIsNotAMemberOf($countryName, $economicAreaName)
    {
        $country = $this->getCountryByName($countryName);
        $economicArea = $this->getEconomicAreaByName($economicAreaName);

        $ids = $economicArea->getMembers()->map(function (EconomicAreaMembership $membership) {return (string) $membership->getCountry()->getId(); })->toArray();

        if (in_array((string) $country->getId(), $ids)) {
            var_dump($this->getJsonContent());
            throw new \Exception('Invalid');
        }
    }

    /**
     * @throws \Exception
     */
    public function getEconomicAreaByName($name): EconomicArea
    {
        $economicArea = $this->economicAreaRepository->findOneBy(['name' => $name]);

        if (!$economicArea instanceof EconomicArea) {
            throw new \Exception('Economic Area not found');
        }

        $this->economicAreaRepository->getEntityManager()->refresh($economicArea);

        return $economicArea;
    }

    /**
     * @Given there are the following economic area memberships:
     */
    public function thereAreTheFollowingEconomicAreaMemberships(TableNode $table)
    {
        $rows = $table->getColumnsHash();

        foreach ($rows as $row) {
            $membership = new EconomicAreaMembership();
            $membership->setEconomicArea($this->getEconomicAreaByName($row['Economic Area']));
            $membership->setCountry($this->getCountryByName($row['Country']));
            $membership->setJoinedAt(new \DateTime($row['Joined At']));
            $membership->setCreatedAt(new \DateTime());

            $this->economicAreaRepository->getEntityManager()->persist($membership);
        }
        $this->economicAreaRepository->getEntityManager()->flush();
    }

    /**
     * @When I delete :arg1 from the economic area :arg2
     */
    public function iDeleteFromTheEconomicArea($countryName, $economicAreaName)
    {
        $country = $this->getCountryByName($countryName);
        $economicArea = $this->getEconomicAreaByName($economicAreaName);
        $membership = $this->economicAreaMembershipRepository->findOneBy(['country' => $country, 'economicArea' => $economicArea]);

        $this->sendJsonRequest('POST', '/app/economic-area/member/'.$membership->getId().'/delete');
    }

    /**
     * @When I view the member :arg1 from the economic area :arg2
     */
    public function iViewTheMemberFromTheEconomicArea($countryName, $economicAreaName)
    {
        $country = $this->getCountryByName($countryName);
        $economicArea = $this->getEconomicAreaByName($economicAreaName);
        $membership = $this->economicAreaMembershipRepository->findOneBy(['country' => $country, 'economicArea' => $economicArea]);

        $this->sendJsonRequest('GET', '/app/economic-area/member/'.$membership->getId().'/view');
    }

    /**
     * @Then I will see see that member joined at :arg1
     */
    public function iWillSeeSeeThatMemberJoinedAt($date)
    {
        $data = $this->getJsonContent();

        $joinedAt = new \DateTime($data['joined_at']);
        if ($joinedAt->format('Y-m-d') !== $date) {
            throw new \Exception(sprintf('Got %s but expected %s', $joinedAt->format('Y-m-d'), $date));
        }
    }

    /**
     * @When I update :arg1 from the economic area :arg2
     */
    public function iUpdateFromTheEconomicArea($countryName, $economicAreaName, TableNode $table)
    {
        $data = $table->getRowsHash();
        $country = $this->getCountryByName($countryName);
        $economicArea = $this->getEconomicAreaByName($economicAreaName);
        $membership = $this->economicAreaMembershipRepository->findOneBy(['country' => $country, 'economicArea' => $economicArea]);
        $payload = [
            'joined_at' => (new \DateTime($data['Joined At']))->format(\DATE_RFC3339_EXTENDED),
            'left_at' => (new \DateTime($data['Left At']))->format(\DATE_RFC3339_EXTENDED),
        ];

        $this->sendJsonRequest('POST', '/app/economic-area/member/'.$membership->getId().'/update', $payload);
    }

    /**
     * @Then the membership for :arg1 to :arg2 will be marked as left at as :arg3
     */
    public function theMembershipForToWillBeMarkedAsLeftAtAs($countryName, $economicAreaName, $date)
    {
        $country = $this->getCountryByName($countryName);
        $economicArea = $this->getEconomicAreaByName($economicAreaName);
        /** @var EconomicAreaMembership $membership */
        $membership = $this->economicAreaMembershipRepository->findOneBy(['country' => $country, 'economicArea' => $economicArea]);

        $this->economicAreaMembershipRepository->getEntityManager()->refresh($membership);

        if ($date !== $membership->getLeftAt()?->format('Y-m-d')) {
            throw new \Exception(sprintf("Expected '%s' but got '%s'", $date, $membership->getLeftAt()?->format('Y-m-d')));
        }
    }
}
