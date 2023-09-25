<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\Subscriptions;

use App\Entity\MassSubscriptionChange;
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
}
