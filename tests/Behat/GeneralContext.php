<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Session;
use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Country;
use BillaBear\Entity\CountryTaxRule;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Entity\Settings;
use BillaBear\Entity\TaxType;
use Doctrine\ORM\EntityManagerInterface;
use Parthenon\Common\Address;

class GeneralContext implements Context
{
    use SendRequestTrait;

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
        $brand->getNotificationSettings()->setSubscriptionCancellation(true);
        $brand->getNotificationSettings()->setSubscriptionCreation(true);
        $brand->getNotificationSettings()->setExpiringCardWarning(true);
        $brand->getNotificationSettings()->setExpiringCardDayBefore(true);

        $em->persist($brand);
        $em->flush();

        $settings = new Settings();
        $settings->setTag(Settings::DEFAULT_TAG);
        $settings->setNotificationSettings(new Settings\NotificationSettings());
        $settings->setSystemSettings(new Settings\SystemSettings());
        $settings->getSystemSettings()->setUseStripeBilling(true);
        $settings->getSystemSettings()->setMainCurrency('USD');
        $settings->getSystemSettings()->setSystemUrl('http://test.example.org/');
        $settings->getSystemSettings()->setInvoiceNumberGeneration('random');
        $settings->setTaxSettings(new Settings\TaxSettings());
        $settings->getTaxSettings()->setTaxCustomersWithTaxNumbers(true);

        $em->persist($settings);
        $em->flush();

        $expiringCardWarning = new EmailTemplate();
        $expiringCardWarning->setName(EmailTemplate::NAME_PAYMENT_METHOD_EXPIRY_WARNING);
        $expiringCardWarning->setBrand($brand);
        $expiringCardWarning->setLocale('en');
        $expiringCardWarning->setSubject('Payment card Expiring');
        $expiringCardWarning->setTemplateBody('Body here');
        $expiringCardWarning->setUseEmspTemplate(false);

        $em->persist($expiringCardWarning);

        $validWarning = new EmailTemplate();
        $validWarning->setName(EmailTemplate::NAME_PAYMENT_METHOD_DAY_BEFORE_WARNING);
        $validWarning->setBrand($brand);
        $validWarning->setLocale('en');
        $validWarning->setSubject('Payment card Expiring');
        $validWarning->setTemplateBody('Body here');
        $validWarning->setUseEmspTemplate(false);

        $em->persist($validWarning);

        $notValidWarning = new EmailTemplate();
        $notValidWarning->setName(EmailTemplate::NAME_PAYMENT_METHOD_DAY_BEFORE_NOT_VALID_WARNING);
        $notValidWarning->setBrand($brand);
        $notValidWarning->setLocale('en');
        $notValidWarning->setSubject('Payment card Expiring');
        $notValidWarning->setTemplateBody('Body here');
        $notValidWarning->setUseEmspTemplate(false);

        $em->persist($notValidWarning);

        $taxType = new TaxType();
        $taxType->setName('default');
        $taxType->setDefault(true);

        $em->persist($taxType);

        $country = new Country();
        $country->setName('USA');
        $country->setIsoCode('US');
        $country->setCurrency('USD');
        $country->setInEu(false);
        $country->setEnabled(true);
        $country->setCreatedAt(new \DateTime());
        $country->setThreshold(0);
        $em->persist($country);

        $countryTaxRule = new CountryTaxRule();
        $countryTaxRule->setCountry($country);
        $countryTaxRule->setTaxType($taxType);
        $countryTaxRule->setTaxRate(10.3);
        $countryTaxRule->setCreatedAt(new \DateTime());
        $countryTaxRule->setValidFrom(new \DateTime('-1 year'));
        $countryTaxRule->setIsDefault(true);
        $em->persist($countryTaxRule);

        $country = new Country();
        $country->setName('Germany');
        $country->setIsoCode('DE');
        $country->setCurrency('EUR');
        $country->setInEu(true);
        $country->setEnabled(true);
        $country->setCreatedAt(new \DateTime());
        $country->setThreshold(0);
        $em->persist($country);

        $countryTaxRule = new CountryTaxRule();
        $countryTaxRule->setCountry($country);
        $countryTaxRule->setTaxType($taxType);
        $countryTaxRule->setTaxRate(10.3);
        $countryTaxRule->setCreatedAt(new \DateTime());
        $countryTaxRule->setValidFrom(new \DateTime('-1 year'));
        $countryTaxRule->setIsDefault(true);
        $em->persist($countryTaxRule);

        $country = new Country();
        $country->setName('France');
        $country->setIsoCode('FR');
        $country->setCurrency('EUR');
        $country->setInEu(true);
        $country->setEnabled(true);
        $country->setCreatedAt(new \DateTime());
        $country->setThreshold(0);
        $em->persist($country);

        $countryTaxRule = new CountryTaxRule();
        $countryTaxRule->setCountry($country);
        $countryTaxRule->setTaxType($taxType);
        $countryTaxRule->setTaxRate(10.3);
        $countryTaxRule->setCreatedAt(new \DateTime());
        $countryTaxRule->setValidFrom(new \DateTime('-1 year'));
        $countryTaxRule->setIsDefault(true);
        $em->persist($countryTaxRule);

        $country = new Country();
        $country->setName('United Kingdom');
        $country->setIsoCode('GB');
        $country->setCurrency('GBP');
        $country->setInEu(false);
        $country->setEnabled(true);
        $country->setCreatedAt(new \DateTime());
        $country->setThreshold(0);
        $em->persist($country);

        $countryTaxRule = new CountryTaxRule();
        $countryTaxRule->setCountry($country);
        $countryTaxRule->setTaxType($taxType);
        $countryTaxRule->setTaxRate(20);
        $countryTaxRule->setCreatedAt(new \DateTime());
        $countryTaxRule->setValidFrom(new \DateTime('-1 year'));
        $countryTaxRule->setIsDefault(true);
        $em->persist($countryTaxRule);

        $em->flush();

        $this->authenticate(null);
        $this->isStripe(false);
    }

    /**
     * @When I use the API key :arg1
     */
    public function iUseTheApiKey($arg1)
    {
        $this->authenticate($arg1);
        $this->sendJsonRequest('GET', '/api/v1/customer');
    }

    /**
     * @Then I will get an unauthorised error response
     */
    public function iWillGetAnUnauthorisedErrorResponse()
    {
        $statusCode = $this->session->getStatusCode();

        if (401 != $statusCode) {
            throw new \Exception('Did not get an error code: '.$statusCode);
        }
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

    /**
     * @Then I will get a valid response
     */
    public function iWillGetAValidResponse()
    {
        $statusCode = $this->session->getStatusCode();

        if ($statusCode > 299) {
            var_dump($this->getJsonContent());
            throw new \Exception('Did not get a valid code');
        }
    }
}
