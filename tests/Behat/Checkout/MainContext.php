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

namespace App\Tests\Behat\Checkout;

use App\Entity\BrandSettings;
use App\Entity\Checkout;
use App\Entity\Customer;
use App\Repository\Orm\BrandSettingsRepository;
use App\Repository\Orm\CheckoutRepository;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\QuoteRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Repository\Orm\UserRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\Quote\QuoteTrait;
use App\Tests\Behat\SendRequestTrait;
use App\Tests\Behat\Subscriptions\SubscriptionTrait;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class MainContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use SubscriptionTrait;
    use QuoteTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private PriceRepository $priceRepository,
        private SubscriptionPlanRepository $planServiceRepository,
        private QuoteRepository $quoteRepository,
        private CheckoutRepository $checkoutRepository,
        private UserRepository $userRepository,
        private BrandSettingsRepository $brandSettingsRepository,
    ) {
    }

    private ?string $name = null;
    private ?Customer $customer = null;
    private array $subscriptions = [];
    private array $items = [];
    private ?\DateTime $expiresAt = null;
    private bool $permanent = false;
    private ?BrandSettings $brandSettings = null;

    /**
     * @BeforeScenario
     */
    public function startUp(BeforeScenarioScope $event)
    {
        $this->name = null;
        $this->customer = null;
        $this->subscriptions = [];
        $this->items = [];
        $this->expiresAt = null;
        $this->dueAt = null;
        $this->permanent = false;
        $this->brandSettings = null;
    }

    /**
     * @Given I start creating a checkout called :arg1
     */
    public function iStartCreatingACheckoutCalled($name)
    {
        $this->name = $name;
    }

    /**
     * @Given I add a subscription to :arg1 at :arg4 in :arg2 per :arg3 to checkout
     */
    public function iAddASubscriptionToAtInPerToCheckout($planName, $amount, $currency, $schedule)
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
            'seat_number' => null,
        ];
    }

    /**
     * @Given I add a one-off fee of :arg3 in :arg1 for :arg2
     */
    public function iAddAOneOffFeeOfInFor($amount, $currency, $description)
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
     * @Given I set the brand for the checkout as :arg1
     */
    public function iSetTheBrandForTheCheckoutAs($brand)
    {
        $brand = $this->brandSettingsRepository->findOneBy(['brandName' => $brand]);

        if (!$brand instanceof BrandSettings) {
            throw new \Exception('No brand found');
        }

        $this->brandSettings = $brand;
    }

    /**
     * @Given I set the checkout to be permanent
     */
    public function iSetTheCheckoutToBePermanent()
    {
        $this->permanent = true;
    }

    /**
     * @When I create the checkout
     */
    public function iCreateTheCheckout()
    {
        if (empty($this->subscriptions) && empty($this->items)) {
            throw new \Exception('No subscriptions or items');
        }

        $payload = [
            'name' => $this->name,
            'customer' => $this->customer?->getId(),
            'subscriptions' => [],
            'items' => [],
            'due_date' => $this->expiresAt?->format(\DATE_RFC3339_EXTENDED),
            'permanent' => $this->permanent,
            'brand' => $this->brandSettings?->getCode(),
        ];

        foreach ($this->subscriptions as $subscription) {
            $payload['subscriptions'][] = [
                'plan' => (string) $subscription['plan']->getId(),
                'price' => (string) $subscription['price']->getId(),
                'seat_number' => $subscription['seat_number'] ?? null,
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

        $this->sendJsonRequest('POST', '/app/checkout/create', $payload);
    }

    /**
     * @Then there should be a permanent checkout called :arg1
     */
    public function thereShouldBeAPermanentCheckoutCalled($name)
    {
        $checkout = $this->getCheckoutByName($name);

        if (!$checkout->isPermanent()) {
            throw new \Exception('Is not permanent');
        }
    }

    /**
     * @Then the checkout :arg1 should have a payment amount of :arg3 :arg2
     */
    public function theCheckoutShouldHaveAPaymentAmountOf($checkoutName, $amount, $currency)
    {
        $checkout = $this->getCheckoutByName($checkoutName);

        if ($checkout->getCurrency() !== $currency) {
            throw new \Exception(sprintf('Expected %s but got %s', $currency, $checkout->getCurrency()));
        }

        if ($checkout->getAmountDue() !== intval($amount)) {
            throw new \Exception(sprintf('Expected %d but got %d', $amount, $checkout->getAmountDue()));
        }
    }

    /**
     * @throws \Exception
     */
    public function getCheckoutByName($name): Checkout
    {
        $checkout = $this->checkoutRepository->findOneBy(['name' => $name]);

        if (!$checkout instanceof Checkout) {
            var_dump($this->session->getPage()->getContent());
            throw new \Exception('No checkout found');
        }

        return $checkout;
    }

    /**
     * @Given a permanent checkout called :arg2 exists in :arg3:
     */
    public function aCheckoutForCalledExistsIn($name, $currency, TableNode $table)
    {
        $checkout = new Checkout();
        $checkout->setName($name);
        $checkout->setSlug(bin2hex(random_bytes(42)));
        $checkout->setPermanent(true);
        $brand = $this->brandSettingsRepository->findOneBy(['brandName' => 'Default']);
        $checkout->setBrandSettings($brand);

        $total = 0;
        $subTotal = 0;
        $vatTotal = 0;
        $lines = [];

        $billingAdmin = $this->userRepository->findOneBy([]);
        $checkout->setCreatedBy($billingAdmin);

        foreach ($table->getColumnsHash() as $row) {
            $total += $row['Total'];
            $subTotal += $row['Sub Total'];
            $vatTotal += $row['Vat Total'];

            $checkoutLine = new \App\Entity\CheckoutLine();
            $checkoutLine->setCheckout($checkout);
            $checkoutLine->setCurrency($currency);
            $checkoutLine->setDescription($row['Description']);
            $checkoutLine->setTotal(intval($row['Total']));
            $checkoutLine->setSubTotal(intval($row['Sub Total']));
            $checkoutLine->setTaxTotal(intval($row['Vat Total']));
            $checkoutLine->setIncludeTax('true' === strtolower($row['Include Tax'] ?? 'false'));

            $lines[] = $checkoutLine;
        }

        $checkout->setLines($lines);
        $checkout->setAmountDue($total);
        $checkout->setTotal($total);
        $checkout->setSubTotal($subTotal);
        $checkout->setTaxTotal($vatTotal);
        $checkout->setCurrency($currency);
        $checkout->setCreatedAt(new \DateTime());
        $checkout->setUpdatedAt(new \DateTime());

        $this->quoteRepository->getEntityManager()->persist($checkout);
        $this->quoteRepository->getEntityManager()->flush();
    }

    /**
     * @When I submit the customer in the portal checkout for :arg1
     */
    public function iSubmitTheCustomerInThePortalCheckoutFor($checkoutName, TableNode $table)
    {
        $checkout = $this->getCheckoutByName($checkoutName);

        $row = $table->getRowsHash();
        $this->sendJsonRequest('POST', '/public/checkout/'.$checkout->getSlug().'/customer', [
            'email' => $row['Email'],
            'address' => [
                'country' => $row['Country'],
            ],
        ]);
    }

    /**
     * @Then the response should have the stripe config
     */
    public function theResponseShouldHaveTheStripeConfig()
    {
        $data = $this->getJsonContent();

        if (!isset($data['stripe'])) {
            throw new \Exception('No stripe data');
        }
    }

    /**
     * @When I view the checkout list in the APP
     */
    public function iViewTheCheckoutListInTheApp()
    {
        $this->sendJsonRequest('GET', '/app/checkout');
    }

    /**
     * @Then I will see a checkout in the list called :arg1
     */
    public function iWillSeeACheckoutInTheListCalled($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['data'] as $item) {
            if ($item['name'] === $arg1) {
                return;
            }
        }

        throw new \Exception("Can't find such a checkout");
    }

    /**
     * @When I view the portal checkout for :arg1
     */
    public function iViewThePortalCheckoutFor($checkoutName)
    {
        $checkout = $this->getCheckoutByName($checkoutName);

        $this->sendJsonRequest('GET', '/public/checkout/'.$checkout->getSlug().'/view');
    }

    /**
     * @Then I will see a checkout
     */
    public function iWillSeeACheckout()
    {
        $data = $this->getJsonContent();

        if (!isset($data['checkout'])) {
            throw new \Exception("Can't find checkout");
        }
    }

    /**
     * @When I view the checkout :arg1
     */
    public function iViewTheCheckout($checkoutName)
    {
        $checkout = $this->getCheckoutByName($checkoutName);

        $this->sendJsonRequest('GET', '/app/checkout/'.$checkout->getId().'/view');
    }

    /**
     * @Then I will see a line item with the description :arg1
     */
    public function iWillSeeALineItemWithTheDescription($arg1)
    {
        $data = $this->getJsonContent();

        foreach ($data['checkout']['lines'] as $line) {
            if ($line['description'] === $arg1) {
                return;
            }
        }

        throw new \Exception("Can't find line with that description");
    }
}