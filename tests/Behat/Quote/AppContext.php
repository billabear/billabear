<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Quote;

use App\Entity\Quote;
use App\Repository\Orm\CustomerRepository;
use App\Repository\Orm\QuoteRepository;
use App\Repository\Orm\UserRepository;
use App\Tests\Behat\Customers\CustomerTrait;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class AppContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use QuoteTrait;

    public function __construct(
        private QuoteRepository $quoteRepository,
        private Session $session,
        private CustomerRepository $customerRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @Given a quote for :arg1 exists in :arg2:
     */
    public function aQuoteForExists($customerEmail, $currency, TableNode $table)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $quote = new Quote();
        $quote->setCustomer($customer);

        $total = 0;
        $subTotal = 0;
        $vatTotal = 0;
        $lines = [];

        $billingAdmin = $this->userRepository->findOneBy([]);
        $quote->setCreatedBy($billingAdmin);

        foreach ($table->getColumnsHash() as $row) {
            $total += $row['Total'];
            $subTotal += $row['Sub Total'];
            $vatTotal += $row['Vat Total'];

            $quoteLine = new \App\Entity\QuoteLine();
            $quoteLine->setCurrency($currency);
            $quoteLine->setTotal(intval($row['Total']));
            $quoteLine->setSubTotal(intval($row['Sub Total']));
            $quoteLine->setTaxTotal(intval($row['Vat Total']));
            $quoteLine->setIncludeTax('true' === strtolower($row['Include Tax'] ?? 'false'));

            $lines[] = $quoteLine;
        }

        $quote->setLines($lines);
        $quote->setAmountDue($total);
        $quote->setTotal($total);
        $quote->setSubTotal($subTotal);
        $quote->setTaxTotal($vatTotal);
        $quote->setCurrency($currency);
        $quote->setCreatedAt(new \DateTime());
        $quote->setUpdatedAt(new \DateTime());

        $this->quoteRepository->getEntityManager()->persist($quote);
        $this->quoteRepository->getEntityManager()->flush();
    }

    /**
     * @When I view the quote for :arg1
     */
    public function iViewTheQuoteFor($customerEmail)
    {
        $customer = $this->getCustomerByEmail($customerEmail);
        $quote = $this->quoteRepository->findOneBy(['customer' => $customer]);
        $this->sendJsonRequest('GET', '/app/quotes/'.$quote->getId().'/view');
    }

    /**
     * @Then I will see a quote for a total of :arg1
     */
    public function iWillSeeAQuoteForATotalOf($total)
    {
        $data = $this->getJsonContent();

        if ($data['quote']['total'] != $total) {
            throw new \Exception('Incorrect total');
        }
    }

    /**
     * @When I view the quotes via the APP
     */
    public function iViewTheQuotesViaTheApp()
    {
        $this->sendJsonRequest('GET', '/app/quotes');
    }

    /**
     * @Then I will see a :arg1 quotes
     */
    public function iWillSeeAQuotes($arg1)
    {
        $data = $this->getJsonContent();
        if (count($data['data']) != $arg1) {
            throw new \Exception('Got a different count - '.count($data['data']));
        }
    }

    /**
     * @Then the latest quote for :arg1 will expire in :arg2
     */
    public function theLatestQuoteForWillExpireIn($email, $dateTimeInput)
    {
        $customer = $this->getCustomerByEmail($email);
        $quote = $this->getLatestQuoteForCustomer($customer);
        $dateTime = new \DateTime($dateTimeInput);

        if ($quote->getExpiresAt() == $dateTime) {
            throw new \Exception('Quote does not have the same value');
        }
    }
}
