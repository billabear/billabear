<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\SubscriptionPlan;

use App\Entity\SubscriptionPlan;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\ProductRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Tests\Behat\Features\FeatureTrait;
use App\Tests\Behat\Products\ProductTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Repository\Orm\SubscriptionFeatureServiceRepository;

class AppContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;
    use FeatureTrait;
    use SubscriptionPlanTrait;

    public function __construct(
        private Session $session,
        private SubscriptionPlanRepository $subscriptionPlanRepository,
        private ProductRepository $productRepository,
        private SubscriptionFeatureServiceRepository $subscriptionFeatureRepository,
        private SubscriptionPlanRepository $planServiceRepository,
        private PriceRepository $priceRepository,
    ) {
    }

    /**
     * @When I create a Subscription Plan for product :arg1 with a feature :arg2 and a limit for :arg3 with a limit of :arg5 and price :arg4 with:
     */
    public function iCreateASubscriptionPlanForProductWithAFeatureAndALimitForWithALimitOfAndPriceWith($productName, $featureName, $limitFeatureName, $limit, $price, TableNode $table)
    {
        $data = $table->getRowsHash();

        $product = $this->getProductByName($productName);
        $feature = $this->getFeatureByName($featureName);
        $limitFeature = $this->getFeatureByName($limitFeatureName);
        $price = $this->priceRepository->findOneBy(['product' => $product]);

        $payload = [
            'name' => $data['Name'],
            'public' => 'true' === strtolower($data['Public']),
            'per_seat' => 'true' === strtolower($data['Per Seat']),
            'user_count' => intval($data['User Count']),
            'code_name' => $data['Code Name'] ?? null,
            'prices' => [
                ['id' => $price->getId()],
            ],
            'features' => [
                ['id' => (string) $feature->getId()],
            ],
            'limits' => [
                [
                    'feature' => ['id' => (string) $limitFeature->getId()],
                    'limit' => (int) $limit,
                ],
            ],
        ];

        $this->sendJsonRequest('POST', '/app/product/'.$product->getId().'/plan', $payload);
    }

    /**
     * @When I update a Subscription Plan :arg1:
     */
    public function iUpdateASubscriptionPlan($planName, TableNode $table)
    {
        $subscriptionPlan = $this->findSubscriptionPlanByName($planName);
        $product = $subscriptionPlan->getProduct();
        $price = $this->priceRepository->findOneBy(['product' => $product]);
        $feature = $subscriptionPlan->getFeatures()->current();
        /** @var SubscriptionPlanLimit $limitFeature */
        $limitFeature = $subscriptionPlan->getLimits()->current();
        $data = $table->getRowsHash();
        $payload = [
            'name' => $data['Name'],
            'public' => 'true' === strtolower($data['Public']),
            'per_seat' => 'true' === strtolower($data['Per Seat']),
            'user_count' => intval($data['User Count']),
            'code_name' => $data['Code Name'] ?? null,
            'prices' => [
                ['id' => $price->getId()],
            ],
            'features' => [
                ['id' => (string) $feature->getId()],
            ],
            'limits' => [
                [
                    'feature' => ['id' => (string) $limitFeature->getSubscriptionFeature()->getId()],
                    'limit' => (int) $limitFeature->getLimit(),
                ],
            ],
        ];

        $this->sendJsonRequest('POST', '/app/product/'.$product->getId().'/plan/'.$subscriptionPlan->getId().'/update', $payload);
    }

    /**
     * @Given a Subscription Plan exists for product :arg1 with a feature :arg2 and a limit for :arg3 with a limit of :arg5 and price :arg4 with:
     */
    public function aSubscriptionPlanExistsForProductWithAFeatureAndALimitForWithALimitOfAndPriceWith($productName, $featureName, $limitFeatureName, $limit, $price, TableNode $table)
    {
        $data = $table->getRowsHash();

        $product = $this->getProductByName($productName);
        $feature = $this->getFeatureByName($featureName);
        $limitFeature = $this->getFeatureByName($limitFeatureName);

        $subscriptionLimit = new SubscriptionPlanLimit();
        $subscriptionLimit->setSubscriptionFeature($limitFeature);
        $subscriptionLimit->setLimit(intval($limit));

        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setName($data['Name']);
        $subscriptionPlan->setPublic('true' === strtolower($data['Public']));
        $subscriptionPlan->setPerSeat('true' === strtolower($data['Per Seat']));
        $subscriptionPlan->setFree('true' === strtolower($data['Free'] ?? 'false'));
        $subscriptionPlan->setUserCount(intval($data['User Count'] ?? 0));
        $subscriptionPlan->setProduct($product);
        $subscriptionPlan->addFeature($feature);
        $subscriptionPlan->addLimit($subscriptionLimit);
        $subscriptionPlan->setCodeName($data['Code Name'] ?? null);

        $this->subscriptionFeatureRepository->getEntityManager()->persist($subscriptionPlan);
        $this->subscriptionFeatureRepository->getEntityManager()->flush();
    }

    /**
     * @Given a Subscription Plan exists for product :arg1 with a feature :arg2 and a limit for :arg3 with a limit of :arg5 and price :arg6 in :arg4 with:
     */
    public function aSubscriptionPlanExistsForProductWithAFeatureAndALimitForWithALimitOfAndPriceInWith($productName, $featureName, $limitFeatureName, $limit, $price, $currency, TableNode $table)
    {
        $data = $table->getRowsHash();

        $product = $this->getProductByName($productName);
        $feature = $this->getFeatureByName($featureName);
        $limitFeature = $this->getFeatureByName($limitFeatureName);
        $price = $this->priceRepository->findOneBy(['amount' => $price, 'currency' => $currency]);

        $subscriptionLimit = new SubscriptionPlanLimit();
        $subscriptionLimit->setSubscriptionFeature($limitFeature);
        $subscriptionLimit->setLimit(intval($limit));

        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setName($data['Name']);
        $subscriptionPlan->setPublic('true' === strtolower($data['Public']));
        $subscriptionPlan->setPerSeat('true' === strtolower($data['Per Seat']));
        $subscriptionPlan->setFree('true' === strtolower($data['Free'] ?? 'false'));
        $subscriptionPlan->setUserCount(intval($data['User Count']));
        $subscriptionPlan->setCodeName($data['Code Name'] ?? null);
        $subscriptionPlan->setProduct($product);
        $subscriptionPlan->addFeature($feature);
        $subscriptionPlan->addLimit($subscriptionLimit);
        $subscriptionPlan->addPrice($price);

        $this->subscriptionFeatureRepository->getEntityManager()->persist($subscriptionPlan);
        $this->subscriptionFeatureRepository->getEntityManager()->flush();
    }

    /**
     * @Given a Subscription Plan exists for product :arg1 with a feature :arg2 and a limit for :arg3 with a limit of :arg5 and price :arg6 in :arg4 monthly and :arg7 yearly with:
     */
    public function aSubscriptionPlanExistsForProductWithAFeatureAndALimitForWithALimitOfAndPriceInMonthlyAndYearlyWith($productName, $featureName, $limitFeatureName, $limit, $price, $currency, $yearlyPrice, TableNode $table)
    {
        $data = $table->getRowsHash();

        $product = $this->getProductByName($productName);
        $feature = $this->getFeatureByName($featureName);
        $limitFeature = $this->getFeatureByName($limitFeatureName);
        $price = $this->priceRepository->findOneBy(['amount' => $price, 'currency' => $currency]);
        $yearlyPrice = $this->priceRepository->findOneBy(['amount' => $yearlyPrice, 'currency' => $currency]);

        $subscriptionLimit = new SubscriptionPlanLimit();
        $subscriptionLimit->setSubscriptionFeature($limitFeature);
        $subscriptionLimit->setLimit(intval($limit));

        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setName($data['Name']);
        $subscriptionPlan->setPublic('true' === strtolower($data['Public']));
        $subscriptionPlan->setPerSeat('true' === strtolower($data['Per Seat']));
        $subscriptionPlan->setFree('true' === strtolower($data['Free'] ?? 'false'));
        $subscriptionPlan->setUserCount(intval($data['User Count']));
        $subscriptionPlan->setProduct($product);
        $subscriptionPlan->addFeature($feature);
        $subscriptionPlan->addLimit($subscriptionLimit);
        $subscriptionPlan->addPrice($price);
        $subscriptionPlan->addPrice($yearlyPrice);

        $this->subscriptionFeatureRepository->getEntityManager()->persist($subscriptionPlan);
        $this->subscriptionFeatureRepository->getEntityManager()->flush();
    }

    /**
     * @When I delete the subscription plan :arg1
     */
    public function iDeleteTheSubscriptionPlan($planName)
    {
        $plan = $this->findSubscriptionPlanByName($planName);
        $product = $plan->getProduct();
        $this->sendJsonRequest('DELETE', '/app/product/'.$product->getId().'/plan/'.$plan->getId());
    }

    /**
     * @Then the subscription plan :arg1 should be marked as deleted
     */
    public function theSubscriptionPlanShouldBeMarkedAsDeleted($planName)
    {
        $plan = $this->findSubscriptionPlanByName($planName);

        if (!$plan->isDeleted()) {
            throw new \Exception('Not marked as deleted');
        }
    }

    /**
     * @When I view the subscription plan :arg1
     */
    public function iViewTheSubscriptionPlan($planName)
    {
        $plan = $this->findSubscriptionPlanByName($planName);
        $product = $plan->getProduct();
        $this->sendJsonRequest('GET', '/app/product/'.$product->getId().'/plan/'.$plan->getId());
    }

    /**
     * @Then the user count in the response should be :arg1
     */
    public function thereShouldBeAFor($value)
    {
        $content = $this->getJsonContent();

        if ($content['subscription_plan']['user_count'] != $value) {
            throw new \Exception("Can't find data");
        }
    }

    /**
     * @Then there should be a subscription plan called :arg1
     */
    public function thereShouldBeASubscriptionPlanCalled($planName)
    {
        $this->findSubscriptionPlanByName($planName);
    }

    /**
     * @Then there should not be a subscription plan called :arg1
     */
    public function thereShouldNotBeASubscriptionPlanCalled($planName)
    {
        try {
            $this->findSubscriptionPlanByName($planName);
        } catch (\Throwable $e) {
            return;
        }
        throw new \Exception('Plan found');
    }

    /**
     * @Then the subscription plan :arg1 should have a feature :arg2
     */
    public function theSubscriptionPlanShouldHaveAFeature($planName, $featureName)
    {
        $plan = $this->findSubscriptionPlanByName($planName);

        /** @var SubscriptionFeature $feature */
        foreach ($plan->getFeatures() as $feature) {
            if ($feature->getName() == $featureName) {
                return;
            }
        }

        throw new \Exception('No feature found');
    }

    /**
     * @Then the subscription plan :arg1 should have a limit :arg2 with a limit of :arg3
     */
    public function theSubscriptionPlanShouldHaveALimitWithALimitOf($planName, $featureName, $arg3)
    {
        $plan = $this->findSubscriptionPlanByName($planName);

        /** @var SubscriptionPlanLimit $limit */
        foreach ($plan->getLimits() as $limit) {
            $feature = $limit->getSubscriptionFeature();
            if ($feature->getName() != $featureName) {
                continue;
            }

            if (intval($arg3) === $limit->getLimit()) {
                return;
            } else {
                throw new \Exception(sprintf('Expected %d but got %d', $arg3, $limit->getLimit()));
            }
        }

        throw new \Exception('No limit found');
    }

    /**
     * @Then the subscription plan :arg1 should have the code name :arg2
     */
    public function theSubscriptionPlanShouldHaveTheCodeName($planName, $codeName)
    {
        $plan = $this->findSubscriptionPlanByName($planName);

        if ($plan->getCodeName() !== $codeName) {
            throw new \Exception(sprintf('Found %s instead of %s', $plan->getCodeName(), $codeName));
        }
    }
}
