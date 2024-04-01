<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Subscriptions;

use App\Background\Subscription\MassChange;
use App\Entity\MassSubscriptionChange;
use App\Enum\MassSubscriptionChangeStatus;
use App\Repository\Orm\BrandSettingsRepository;
use App\Repository\Orm\MassSubscriptionChangeRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\SubscriptionPlan\SubscriptionPlanTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class MassChangeContext implements Context
{
    use SendRequestTrait;
    use SubscriptionPlanTrait;

    public function __construct(
        private Session $session,
        private SubscriptionPlanRepository $subscriptionPlanRepository,
        private MassSubscriptionChangeRepository $massSubscriptionChangeRepository,
        private PriceRepository $priceRepository,
        private BrandSettingsRepository $brandSettingsRepository,
        private MassChange $massChange,
    ) {
    }

    /**
     * @When I create a mass subscription change:
     */
    public function iCreateAMassSubscriptionChange(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [];

        if (!isset($data['Date'])) {
            throw new \Exception('Date required');
        }

        $dateTime = new \DateTime($data['Date']);
        $payload['change_date'] = $dateTime->format(\DATE_RFC3339_EXTENDED);

        if (isset($data['Target Brand'])) {
            $brand = $this->brandSettingsRepository->findOneBy(['brandName' => $data['Target Brand']]);
            $payload['target_brand'] = $brand?->getCode();
        }
        if (isset($data['Target Country'])) {
            $payload['target_country'] = $data['Target Country'];
        }

        if (isset($data['Target Subscription Plan'])) {
            if ('invalid' == strtolower($data['Target Subscription Plan'])) {
                $payload['target_plan'] = 'invalid';
            } else {
                $subscriptionPlan = $this->findSubscriptionPlanByName($data['Target Subscription Plan']);
                $payload['target_plan'] = (string) $subscriptionPlan->getId();
            }
        }

        if (isset($data['New Subscription Plan'])) {
            if ('invalid' == strtolower($data['New Subscription Plan'])) {
                $payload['new_plan'] = 'invalid';
            } else {
                $subscriptionPlan = $this->findSubscriptionPlanByName($data['New Subscription Plan']);
                $payload['new_plan'] = (string) $subscriptionPlan->getId();
            }
        }

        if (isset($data['New Price Amount']) || isset($data['New Price Currency']) || isset($data['New Price Schedule'])) {
            if (isset($data['New Price Amount']) && isset($data['New Price Currency']) && isset($data['New Price Schedule'])) {
                $price = $this->priceRepository->findOneBy([
                    'amount' => $data['New Price Amount'],
                    'currency' => $data['New Price Currency'],
                    'schedule' => $data['New Price Schedule'],
                ]);
                $payload['new_price'] = $price?->getId();
            } else {
                throw new \Exception('Not all price data set');
            }
        }
        if (isset($data['Target Price Amount']) || isset($data['Target Price Currency']) || isset($data['Target Price Schedule'])) {
            if (isset($data['Target Price Amount']) && isset($data['Target Price Currency']) && isset($data['Target Price Schedule'])) {
                $price = $this->priceRepository->findOneBy([
                    'amount' => $data['Target Price Amount'],
                    'currency' => $data['Target Price Currency'],
                    'schedule' => $data['Target Price Schedule'],
                ]);
                $payload['target_price'] = $price?->getId();
            } else {
                throw new \Exception('Not all target price data set');
            }
        }

        $this->sendJsonRequest('POST', '/app/subscription/mass-change', $payload);
    }

    /**
     * @Then there should not be a mass subscription change
     */
    public function thereShouldNotBeAMassSubscriptionChange()
    {
        /** @var MassSubscriptionChange $one */
        $one = $this->massSubscriptionChangeRepository->findOneBy([]);

        if ($one) {
            throw new \Exception('Found mass subscription change');
        }
    }

    /**
     * @Then there should be a mass subscription change that contains:
     */
    public function thereShouldBeAMassSubscriptionChangeThatContains(TableNode $table)
    {
        $data = $table->getRowsHash();
        /** @var MassSubscriptionChange $one */
        $one = $this->massSubscriptionChangeRepository->findOneBy([]);

        if (isset($data['Target Brand'])) {
            $brand = $this->brandSettingsRepository->findOneBy(['brandName' => $data['Target Brand']]);
            if ($one->getBrandSettings()?->getId() != $brand->getId()) {
                throw new \Exception('Wrong brand');
            }
        }

        if (isset($data['Target Country'])) {
            if ($one->getTargetCountry() !== $data['Target Country']) {
                throw new \Exception('Wrong target country');
            }
        }

        if (isset($data['Target Subscription Plan'])) {
            $subscriptionPlan = $this->findSubscriptionPlanByName($data['Target Subscription Plan']);
            if ($one->getTargetSubscriptionPlan()?->getId() != $subscriptionPlan->getId()) {
                throw new \Exception('Wrong target subscription plan');
            }
        }

        if (isset($data['New Subscription Plan'])) {
            $subscriptionPlan = $this->findSubscriptionPlanByName($data['New Subscription Plan']);
            if ($one->getNewSubscriptionPlan()?->getId() != $subscriptionPlan->getId()) {
                throw new \Exception('Wrong new subscription plan');
            }
        }

        if (isset($data['New Price Amount'])) {
            if ($one->getNewPrice()?->getAmount() !== intval($data['New Price Amount'])) {
                throw new \Exception('Wrong new price amount');
            }
        }
        if (isset($data['New Price Currency'])) {
            if ($one->getNewPrice()?->getCurrency() !== $data['New Price Currency']) {
                throw new \Exception('Wrong new price Currency');
            }
        }
        if (isset($data['New Price Schedule'])) {
            if ($one->getNewPrice()?->getSchedule() !== $data['New Price Schedule']) {
                throw new \Exception('Wrong new price Schedule');
            }
        }

        if (isset($data['Target Price Amount'])) {
            if ($one->getTargetPrice()?->getAmount() !== intval($data['Target Price Amount'])) {
                throw new \Exception('Wrong Target price amount');
            }
        }
        if (isset($data['Target Price Currency'])) {
            if ($one->getTargetPrice()?->getCurrency() !== $data['Target Price Currency']) {
                throw new \Exception('Wrong Target price Currency');
            }
        }
        if (isset($data['Target Price Schedule'])) {
            if ($one->getTargetPrice()?->getSchedule() !== $data['Target Price Schedule']) {
                throw new \Exception('Wrong Target price Schedule');
            }
        }

        if (isset($data['Date'])) {
            $changeDate = new \DateTime($data['Date']);
            if ($changeDate->format('Y-m-d') != $one->getChangeDate()->format('Y-m-d')) {
                throw new \Exception("Date doesn't match");
            }
        }
    }

    /**
     * @When I process mass change subscriptions
     */
    public function iProcessMassChangeSubscriptions()
    {
        $this->massChange->execute();
    }

    /**
     * @When there are the following mass subscription changes exists:
     */
    public function thereAreTheFollowingMassSubscriptionChangesExists(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $targetPlan = $this->findSubscriptionPlanByName($row['Target Subscription Plan']);
            $newPlan = $this->findSubscriptionPlanByName($row['New Subscription Plan']);

            $entity = new MassSubscriptionChange();

            if (isset($row['New Price Amount']) || isset($row['New Price Currency']) || isset($row['New Price Schedule'])) {
                if (isset($row['New Price Amount']) && isset($row['New Price Currency']) && isset($row['New Price Schedule'])) {
                    $price = $this->priceRepository->findOneBy([
                        'amount' => $row['New Price Amount'],
                        'currency' => $row['New Price Currency'],
                        'schedule' => $row['New Price Schedule'],
                    ]);
                    $entity->setNewPrice($price);
                } else {
                    throw new \Exception('Not all price data set');
                }
            }
            if (isset($row['Target Price Amount']) || isset($row['Target Price Currency']) || isset($row['Target Price Schedule'])) {
                if (isset($row['Target Price Amount']) && isset($row['Target Price Currency']) && isset($row['Target Price Schedule'])) {
                    $price = $this->priceRepository->findOneBy([
                        'amount' => $row['Target Price Amount'],
                        'currency' => $row['Target Price Currency'],
                        'schedule' => $row['Target Price Schedule'],
                    ]);
                    $entity->setTargetPrice($price);
                } else {
                    throw new \Exception('Not all target price data set');
                }
            }

            $entity->setTargetSubscriptionPlan($targetPlan);
            $entity->setNewSubscriptionPlan($newPlan);
            $entity->setCreatedAt(new \DateTime());
            $entity->setStatus(MassSubscriptionChangeStatus::CREATED);
            $entity->setChangeDate(new \DateTime($row['Change Date']));

            $this->massSubscriptionChangeRepository->getEntityManager()->persist($entity);
        }
        $this->massSubscriptionChangeRepository->getEntityManager()->flush();
    }

    /**
     * @When I estimate the new revenue for a mass subscription change:
     */
    public function iEstimateTheNewRevenueForAMassSubscriptionChange(TableNode $table)
    {
        $data = $table->getRowsHash();
        $payload = [];

        if (isset($data['Target Brand'])) {
            $brand = $this->brandSettingsRepository->findOneBy(['brandName' => $data['Target Brand']]);
            $payload['target_brand'] = $brand?->getCode();
        }
        if (isset($data['Target Country'])) {
            $payload['target_country'] = $data['Target Country'];
        }

        if (isset($data['Target Subscription Plan'])) {
            if ('invalid' == strtolower($data['Target Subscription Plan'])) {
                $payload['target_plan'] = 'invalid';
            } else {
                $subscriptionPlan = $this->findSubscriptionPlanByName($data['Target Subscription Plan']);
                $payload['target_plan'] = (string) $subscriptionPlan->getId();
            }
        }

        if (isset($data['New Subscription Plan'])) {
            if ('invalid' == strtolower($data['New Subscription Plan'])) {
                $payload['new_plan'] = 'invalid';
            } else {
                $subscriptionPlan = $this->findSubscriptionPlanByName($data['New Subscription Plan']);
                $payload['new_plan'] = (string) $subscriptionPlan->getId();
            }
        }

        if (isset($data['New Price Amount']) || isset($data['New Price Currency']) || isset($data['New Price Schedule'])) {
            if (isset($data['New Price Amount']) && isset($data['New Price Currency']) && isset($data['New Price Schedule'])) {
                $price = $this->priceRepository->findOneBy([
                    'amount' => $data['New Price Amount'],
                    'currency' => $data['New Price Currency'],
                    'schedule' => $data['New Price Schedule'],
                ]);
                $payload['new_price'] = (string) $price?->getId();
            } else {
                throw new \Exception('Not all price data set');
            }
        }
        if (isset($data['Target Price Amount']) || isset($data['Target Price Currency']) || isset($data['Target Price Schedule'])) {
            if (isset($data['Target Price Amount']) && isset($data['Target Price Currency']) && isset($data['Target Price Schedule'])) {
                $price = $this->priceRepository->findOneBy([
                    'amount' => $data['Target Price Amount'],
                    'currency' => $data['Target Price Currency'],
                    'schedule' => $data['Target Price Schedule'],
                ]);
                $payload['target_price'] = (string) $price?->getId();
            } else {
                throw new \Exception('Not all target price data set');
            }
        }

        $this->sendJsonRequest('POST', '/app/subscription/mass-change/estimate', $payload);
    }

    /**
     * @Then I should be told the new revenue generated would be :arg2 :arg1
     */
    public function iShouldBeToldTheNewRevenueGeneratedWouldBe($amount, $currency)
    {
        $data = $this->getJsonContent();
        if ($data['amount'] != intval($amount)) {
            throw new \Exception(sprintf('Expected %d but got %d', $amount, $data['amount']));
        }

        if ($data['currency'] != $data['currency']) {
            throw new \Exception(sprintf('Expected %s but got %s', $currency, $data['currency']));
        }
    }

    /**
     * @When I look at the mass subscription change list
     */
    public function iLookAtTheMassSubscriptionChangeList()
    {
        $this->sendJsonRequest('GET', '/app/subscription/mass-change');
    }

    /**
     * @When I view the mass subscription change with the target subscription plan :arg1
     */
    public function iViewTheMassSubscriptionChangeWithTheTargetSubscriptionPlan($arg1)
    {
        $subscriptionPlan = $this->findSubscriptionPlanByName($arg1);

        $change = $this->massSubscriptionChangeRepository->findOneBy(['targetSubscriptionPlan' => $subscriptionPlan]);

        $this->sendJsonRequest('GET', '/app/subscription/mass-change/'.$change->getId().'/view');
    }

    /**
     * @Then I will see the mass subscription change for the target subscription plan :arg1
     */
    public function iWillSeeTheMassSubscriptionChangeForTheTargetSubscriptionPlan($arg1)
    {
        $subscriptionPlan = $this->findSubscriptionPlanByName($arg1);
        $data = $this->getJsonContent();

        if ($data['mass_change']['target_plan']['id'] !== (string) $subscriptionPlan->getId()) {
            throw new \Exception('Wrong target plan');
        }
    }

    /**
     * @Then I will see there are :arg1 mass subscriptions changes in the list
     */
    public function iWillSeeThereAreMassSubscriptionsChangesInTheList($arg1)
    {
        $data = $this->getJsonContent();

        if (count($data['data']) !== intval($arg1)) {
            throw new \Exception('Wrong count');
        }
    }
}
