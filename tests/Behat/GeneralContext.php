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

namespace App\Tests\Behat;

use App\Entity\BrandSettings;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Session;
use Doctrine\ORM\EntityManagerInterface;
use Parthenon\Common\Address;

class GeneralContext implements Context
{
    private Session $session;
    private EntityManagerInterface $entityManager;

    public function __construct(Session $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * @BeforeScenario
     */
    public function startUp(BeforeScenarioScope $event)
    {
        $em = $this->entityManager;
        $metaData = $em->getMetadataFactory()->getAllMetadata();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);

        if ($this->session->isStarted()) {
            $this->session->stop();
        }
        $this->session->start();

        $brand = new BrandSettings();
        $brand->setCode('default');
        $brand->setBrandName('Default');
        $brand->setAddress(new Address());
        $brand->setEmailAddress('info@example.org');
        $brand->getAddress()->setStreetLineOne('1 Example Way');
        $brand->getAddress()->setCity('Example Town');
        $brand->getAddress()->setCountry('GB');
        $brand->getAddress()->setPostcode('10367');
        $brand->setIsDefault(true);

        $em->persist($brand);
        $em->flush();
    }

    /**
     * @Then I will get an error response
     */
    public function iWillGetAnErrorResponse()
    {
        $statusCode = $this->session->getStatusCode();

        if ($statusCode < 400 || $statusCode >= 500) {
            throw new \Exception('Did not get an error code');
        }
    }
}
