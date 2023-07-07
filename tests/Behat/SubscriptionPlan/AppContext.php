<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\SubscriptionPlan;

use App\Tests\Behat\Features\FeatureTrait;
use App\Tests\Behat\Products\ProductTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\SubscriptionPlanLimit;
use Parthenon\Billing\Repository\Orm\PriceServiceRepository;
use Parthenon\Billing\Repository\Orm\ProductServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionFeatureServiceRepository;
use Parthenon\Billing\Repository\Orm\SubscriptionPlanServiceRepository;

class AppContext implements Context
{
    use SendRequestTrait;
    use ProductTrait;
    use FeatureTrait;

    public function __construct(
        private Session $session,
        private SubscriptionPlanServiceRepository $planRepository,
        private ProductServiceRepository $productRepository,
        private SubscriptionFeatureServiceRepository $subscriptionFeatureRepository,
        private SubscriptionPlanServiceRepository $planServiceRepository,
        private PriceServiceRepository $priceRepository,
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
        $subscriptionPlan->setUserCount(intval($data['User Count']));
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

    protected function findSubscriptionPlanByName(string $planName): SubscriptionPlan
    {
        $subscriptionPlan = $this->planRepository->findOneBy(['name' => $planName]);

        if (!$subscriptionPlan instanceof SubscriptionPlan) {
            throw new \Exception("Can't find plan");
        }

        $this->planRepository->getEntityManager()->refresh($subscriptionPlan);

        return $subscriptionPlan;
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
