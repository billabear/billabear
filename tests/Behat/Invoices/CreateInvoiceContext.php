<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Invoices;

use App\Entity\Customer;
use App\Entity\Quote;
use App\Entity\TaxType;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\PriceRepository;
use App\Repository\Orm\QuoteRepository;
use App\Repository\Orm\SubscriptionPlanRepository;
use App\Repository\Orm\TaxTypeRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\Quote\QuoteTrait;
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
    use QuoteTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private PriceRepository $priceRepository,
        private SubscriptionPlanRepository $planServiceRepository,
        private QuoteRepository $quoteRepository,
        private TaxTypeRepository $taxTypeRepository,
    ) {
    }

    private ?Customer $customer = null;
    private array $subscriptions = [];
    private array $items = [];
    private ?\DateTime $expiresAt = null;
    private ?\DateTime $dueAt = null;

    /**
     * @BeforeScenario
     */
    public function startUp(BeforeScenarioScope $event)
    {
        $this->customer = null;
        $this->subscriptions = [];
        $this->items = [];
        $this->expiresAt = null;
        $this->dueAt = null;
    }

    /**
     * @Given I want to invoice the customer :arg1
     */
    public function iWantToInvoiceTheCustomer($customerEmail)
    {
        $this->customer = $this->getCustomerByEmail($customerEmail);
    }

    /**
     * @Given I want to invoice for a subscription to :arg1 at :arg4 in :arg2 per :arg3 with :arg5 seats
     */
    public function iWantToInvoiceForASubscriptionToAtInPerWithSeats($planName, $amount, $currency, $schedule, $seatNumber)
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
            'seat_number' => intval($seatNumber),
        ];
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
     * @Given I want the invoice to be paid within :arg1
     */
    public function iWantTheInvoiceToBePaidWithin($dateFormat)
    {
        $this->dueAt = new \DateTime('+'.$dateFormat);
    }

    /**
     * @Given I want to invoice for a bespoke one-off fee for :description at :amount in :currency including tax for a digital goods
     */
    public function iWantToInvoiceForABespokeOneOffFeeForAtInIncludingTax($description, $amount, $currency)
    {
        /** @var TaxType $taxType */
        $taxType = $this->taxTypeRepository->findOneBy(['name' => 'Digital Goods']);
        $this->items[] = [
            'description' => $description,
            'amount' => $amount,
            'currency' => $currency,
            'include_tax' => true,
            'tax_type' => (string) $taxType->getId(),
        ];
    }

    /**
     * @Given I want to invoice for a bespoke one-off fee for :description at :amount in :currency including tax for a physical goods
     */
    public function iWantToInvoiceForABespokeOneOffFeeForAtInIncludingTaxPhysical($description, $amount, $currency)
    {
        /** @var TaxType $taxType */
        $taxType = $this->taxTypeRepository->findOneBy(['name' => 'Physical']);
        $this->items[] = [
            'description' => $description,
            'amount' => $amount,
            'currency' => $currency,
            'include_tax' => true,
            'tax_type' => (string) $taxType->getId(),
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
            'due_date' => $this->dueAt?->format(\DATE_RFC3339_EXTENDED),
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

    /**
     * @Given I want to set a time limit of :arg1 for the quote
     */
    public function iWantToSetATimeLimitOfForTheQuote($dateTimeInput)
    {
        $this->expiresAt = new \DateTime($dateTimeInput);
    }

    /**
     * @When I finalise the quote in APP
     */
    public function iFinaliseTheQuoteInApp()
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
            'expires_at' => $this->expiresAt?->format(\DATE_RFC3339_EXTENDED),
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

        $this->sendJsonRequest('POST', '/app/quotes/create', $payload);
    }

    /**
     * @Then there will be a quote for :arg1
     */
    public function thereWillBeAQuoteFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $this->getLatestQuoteForCustomer($customer);
    }

    /**
     * @When I accept and pay for the quote for :arg1
     */
    public function iAcceptAndPayForTheQuoteFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $quote = $this->getLatestQuoteForCustomer($customer);

        $this->sendJsonRequest('POST', '/public/quote/'.$quote->getId().'/pay', ['token' => bin2hex(random_bytes(32))]);
    }

    /**
     * @Then the attempt to pay accept will be rejected
     */
    public function theAttemptToPayAcceptWillBeRejected()
    {
        $statusCode = $this->session->getStatusCode();

        if (406 !== $statusCode) {
            throw new \Exception('Status code is not 406');
        }
    }

    /**
     * @When I view the paylink for the quote for :arg1
     */
    public function iViewThePaylinkForTheQuoteFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $quote = $this->getLatestQuoteForCustomer($customer);

        $this->sendJsonRequest('GET', '/public/quote/'.$quote->getId().'/pay');
    }

    /**
     * @Then the paylink will show that it's expired
     */
    public function thePaylinkWillShowThatItsExpired()
    {
        $data = $this->getJsonContent();

        if (true !== $data['quote']['expired']) {
            throw new \Exception('Is not marked as expired');
        }
    }

    /**
     * @Then the quote for :arg1 should be marked as paid
     */
    public function theQuoteForShouldBeMarkedAsPaid($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $quote = $this->getLatestQuoteForCustomer($customer);

        if (!$quote->isPaid()) {
            throw new \Exception('Quote should be marked as paid');
        }
    }

    /**
     * @Then the latest quote for :arg1 will have amount due as :arg2
     */
    public function theLatestQuoteForWillHaveAmountDueAs($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $quote = $this->getLatestQuoteForCustomer($customer);

        if ($quote->getAmountDue() !== (int) $amount) {
            throw new \Exception(sprintf('Expected %d but got %d', $amount, $quote->getAmountDue()));
        }
    }
}
