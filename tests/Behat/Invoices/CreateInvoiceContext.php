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

namespace App\Tests\Behat\Invoices;

use App\Entity\Customer;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\Subscriptions\SubscriptionTrait;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Session;

class CreateInvoiceContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use SubscriptionTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private PriceRepository $priceRepository,
        private SubscriptionPlanRepository $planServiceRepository,
    ) {
    }

    private ?Customer $customer = null;
    private array $subscriptions = [];
    private array $items = [];

    /**
     * @BeforeScenario
     */
    public function startUp(BeforeScenarioScope $event)
    {
        $this->customer = null;
        $this->subscriptions = [];
        $this->items = [];
    }

    /**
     * @Given I want to invoice the customer :arg1
     */
    public function iWantToInvoiceTheCustomer($customerEmail)
    {
        $this->customer = $this->getCustomerByEmail($customerEmail);
    }

    /**
     * @Given I want to invoice for a subscription to :planName at :priceNumber in :currency per :schedule
     */
    public function iWantToInvoiceForASubscriptionToAtInPer($planName, $amount, $currency, $schedule)
    {
        $subscriptionPlan = $this->planServiceRepository->findOneBy(['name' => $planName]);

        if (!$subscriptionPlan) {
            throw new \Exception(sprintf("Subscription plan for '%s' not found", $planName));
        }

        $price = $this->priceRepository->findOneBy(['amount' => $amount, 'currency' => $currency, 'schedule' => $schedule]);

        if (!$price) {
            throw new \Exception(sprintf('Price for %d in %s per %s not found', $amount, $currency, $schedule));
        }

        $this->subscriptions[] = [
            'plan' => $subscriptionPlan,
            'price' => $price,
        ];
    }

    /**
     * @Given I want to invoice for a bespoke one-off fee for :description at :amount in :currency including tax for a digital goods
     */
    public function iWantToInvoiceForABespokeOneOffFeeForAtInIncludingTax($description, $amount, $currency)
    {
        $this->items[] = [
            'description' => $description,
            'amount' => $amount,
            'currency' => $currency,
            'include_tax' => true,
            'tax_type' => 'digital_goods',
        ];
    }

    /**
     * @Given I want to invoice for a bespoke one-off fee for :description at :amount in :currency including tax for a physical goods
     */
    public function iWantToInvoiceForABespokeOneOffFeeForAtInIncludingTaxPhysical($description, $amount, $currency)
    {
        $this->items[] = [
            'description' => $description,
            'amount' => $amount,
            'currency' => $currency,
            'include_tax' => true,
            'tax_type' => 'physical',
        ];
    }

    /**
     * @When I finalise the invoice in APP
     */
    public function iFinaliseTheInvoiceInApp()
    {
        if (!isset($this->customer)) {
            throw new \Exception('No customer set');
        }

        if (empty($this->subscriptions) && empty($this->items)) {
            throw new \Exception('No subscriptions or items');
        }

        $payload = [
            'customer' => $this->customer->getId(),
            'subscriptions' => [],
            'items' => [],
        ];

        foreach ($this->subscriptions as $subscription) {
            $payload['subscriptions'][] = [
                'plan' => (string) $subscription['plan']->getId(),
                'price' => (string) $subscription['price']->getId(),
            ];
        }

        foreach ($this->items as $item) {
            $payload['items'][] = [
                'description' => $item['description'],
                'amount' => $item['amount'],
                'currency' => $item['currency'],
                'include_tax' => $item['include_tax'],
                'tax_type' => $item['tax_type'],
            ];
        }

        $this->sendJsonRequest('POST', '/app/invoices/create', $payload);
    }

    /**
     * @Then I will get the error that the payment schedules must all match
     */
    public function iWillGetTheErrorThatThePaymentSchedulesMustAllMatch()
    {
        $json = $this->getJsonContent();

        if (empty($json['errors'])) {
            throw new \Exception('No errors');
        }

        if (!isset($json['errors']['subscriptions'])) {
            throw new \Exception('No error for subscriptions');
        }

        if ('Payment schedule is not all the same' !== $json['errors']['subscriptions']) {
            throw new \Exception('Invalid error - '.$json['errors']['subscriptions']);
        }
    }
}
