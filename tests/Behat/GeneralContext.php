<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Session;
use Behat\Step\Given;
use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Entity\Settings;
use BillaBear\Entity\TaxType;
use BillaBear\Invoice\InvoiceGenerationType;
use BillaBear\Logger\Audit\IndexProviderInterface;
use BillaBear\Repository\Orm\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\ClientInterface;
use Parthenon\Common\Address;

class GeneralContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private EntityManagerInterface $entityManager,
        private SettingsRepository $settingsRepository,
        private ClientInterface $client,
        private IndexProviderInterface $indexProvider,
    ) {
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
        $settings->getSystemSettings()->setStripePrivateKey('private-key');
        $settings->getSystemSettings()->setStripePublicKey('public-key');
        $settings->getSystemSettings()->setInvoiceGenerationType(InvoiceGenerationType::PERIODICALLY);
        $settings->getSystemSettings()->setDefaultInvoiceDueTime('30 days');
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

        $validWarning = new EmailTemplate();
        $validWarning->setName(EmailTemplate::NAME_TRIAL_ENDING_WARNING);
        $validWarning->setBrand($brand);
        $validWarning->setLocale('en');
        $validWarning->setSubject('Trial Ending Soon');
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

        $usageWarning = new EmailTemplate();
        $usageWarning->setName(EmailTemplate::NAME_USAGE_WARNING);
        $usageWarning->setBrand($brand);
        $usageWarning->setLocale('en');
        $usageWarning->setSubject('Usage Warning');
        $usageWarning->setTemplateBody('Body here');
        $usageWarning->setUseEmspTemplate(false);

        $em->persist($usageWarning);

        $usageDisable = new EmailTemplate();
        $usageDisable->setName(EmailTemplate::NAME_USAGE_DISABLED);
        $usageDisable->setBrand($brand);
        $usageDisable->setLocale('en');
        $usageDisable->setSubject('Usage Disabled');
        $usageDisable->setTemplateBody('Body here');
        $usageDisable->setUseEmspTemplate(false);

        $em->persist($usageDisable);

        $taxType = new TaxType();
        $taxType->setName('default');
        $taxType->setDefault(true);

        $em->persist($taxType);

        $em->flush();

        $this->authenticate(null);
        $this->isStripe(false);

        $params = ['index' => $this->indexProvider->getIndex(), 'allow_no_indices' => true];
        $result = $this->client->indices()->exists($params);
        if ($result->asBool()) {
            $this->client->indices()->delete($params);
        }
        $this->client->indices()->create(['index' => $this->indexProvider->getIndex()]);
    }

    #[Given('there are no stripe api keys configured')]
    public function thereAreNoStripeApiKeysConfigured(): void
    {
        /** @var Settings $settings */
        $settings = $this->settingsRepository->findOneBy(['tag' => Settings::DEFAULT_TAG]);
        $settings->getSystemSettings()->setStripePrivateKey(null);
        $settings->getSystemSettings()->setStripePublicKey(null);
        $this->entityManager->persist($settings);
        $this->entityManager->flush();
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
