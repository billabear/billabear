<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\CreditNote;

use App\Entity\Credit;
use App\Repository\Orm\CreditRepository;
use App\Repository\Orm\CustomerRepository;
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

    public function __construct(
        private Session $session,
        private CreditRepository $creditRepository,
        private CustomerRepository $customerRepository,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @Given the following credit transactions exist:
     */
    public function theFollowingCreditTransactionsExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $customer = $this->getCustomerByEmail($row['Customer']);
            $credit = new Credit();
            $credit->setCustomer($customer);
            $credit->setType(strtolower($row['Type']));
            $credit->setAmount(intval($row['Amount']));
            $credit->setCurrency(strtoupper($row['Currency']));
            $credit->setUsedAmount(0);
            $credit->setCreatedAt(new \DateTime('now'));
            $credit->setUpdatedAt(new \DateTime('now'));
            $credit->setCreationType(Credit::CREATION_TYPE_AUTOMATED);

            $customer->addCreditAsMoney($credit->asMoney());

            $this->creditRepository->getEntityManager()->persist($credit);
        }

        $this->creditRepository->getEntityManager()->flush();
    }

    /**
     * @When I create a debit for :arg1 for :arg3 in the currency :arg2
     */
    public function iCreateADebitForForInTheCurrency($customerEmail, $amount, $currency)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $payload = [
            'type' => Credit::TYPE_DEBIT,
            'amount' => $amount,
            'currency' => $currency,
        ];

        $this->sendJsonRequest('POST', '/app/customer/'.$customer->getId().'/credit', $payload);
    }

    /**
     * @Then the credit amount for :arg1 should be :arg2
     */
    public function theCreditAmountForShouldBe($customerEmail, $amount)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        if ($amount != $customer->getCreditAmount()) {
            throw new \Exception('Not the correct amount'.$customer->getCreditAmount());
        }
    }

    /**
     * @Then there should be a completely used credit for :arg1 for :arg3 in the currency :arg2
     */
    public function thereShouldBeACompletelyUsedCreditForForInTheCurrency($customerEmail, $amount, $currency)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $creditNote = $this->creditRepository->findOneBy(['customer' => $customer, 'type' => 'credit', 'completelyUsed' => true]);

        if (!$creditNote instanceof Credit) {
            throw new \Exception('No credit note found');
        }

        if ($creditNote->getCurrency() != $currency || $creditNote->getAmount() != $amount) {
            throw new \Exception('Wrong currency or amount');
        }
    }

    /**
     * @Then there should be a completely used debit for :arg1 for :arg3 in the currency :arg2
     */
    public function thereShouldBeACompletelyUsedDebitForForInTheCurrency($customerEmail, $amount, $currency)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $creditNote = $this->creditRepository->findOneBy(['customer' => $customer, 'type' => 'debit', 'completelyUsed' => true]);

        if (!$creditNote instanceof Credit) {
            throw new \Exception('No credit note found');
        }

        if ($creditNote->getCurrency() != $currency || $creditNote->getAmount() != $amount) {
            throw new \Exception('Wrong currency or amount');
        }
    }

    /**
     * @When I create a credit for :arg1 for :arg3 in the currency :arg2
     */
    public function iCreateACreditNoteForForInTheCurrency($customerEmail, $amount, $currency)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $payload = [
            'type' => Credit::TYPE_CREDIT,
            'amount' => $amount,
            'currency' => $currency,
        ];

        $this->sendJsonRequest('POST', '/app/customer/'.$customer->getId().'/credit', $payload);
    }

    /**
     * @Then there should be a credit for :arg1 for :arg3 in the currency :arg2
     */
    public function thereShouldBeACreditNoteForForInTheCurrency($customerEmail, $amount, $currency)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $creditNote = $this->creditRepository->findOneBy(['customer' => $customer]);

        if (!$creditNote instanceof Credit) {
            throw new \Exception('No credit note found');
        }

        if ($creditNote->getCurrency() != $currency || $creditNote->getAmount() != $amount) {
            throw new \Exception('Wrong currency or amount');
        }
    }

    /**
     * @Then there should be a credit created by :arg1
     */
    public function thereShouldBeACreditNoteCreatedBy($email)
    {
        $billingAdmin = $this->userRepository->findOneBy(['email' => $email]);

        $creditNote = $this->creditRepository->findOneBy(['billingAdmin' => $billingAdmin]);

        if (!$creditNote instanceof Credit) {
            throw new \Exception('No credit note found');
        }
    }

    /**
     * @When the following credit exist:
     */
    public function theFollowingCreditNotesExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $customer = $this->getCustomerByEmail($row['Customer']);
            $creditNote = new Credit();
            $creditNote->setCustomer($customer);
            $creditNote->setAmount(intval($row['Amount']));
            $creditNote->setCurrency($row['Currency']);
            $creditNote->setUsedAmount(intval($row['Used Amount'] ?? 0));
            $creditNote->setCompletelyUsed($creditNote->getUsedAmount() === $creditNote->getAmount());
            $creditNote->setCreatedAt(new \DateTime());
            $creditNote->setUpdatedAt(new \DateTime());
            $creditNote->setCreationType(Credit::CREATION_TYPE_AUTOMATED);

            $this->creditRepository->getEntityManager()->persist($creditNote);
        }

        $this->creditRepository->getEntityManager()->flush();
    }

    /**
     * @Then I will see a credit for :arg2 in the currency :arg1
     */
    public function iWillSeeACreditNoteForInTheCurrency($amount, $currency)
    {
        $data = $this->getJsonContent();

        if (!isset($data['credit'])) {
            throw new \Exception('No credit notes');
        }

        foreach ($data['credit'] as $creditNote) {
            if ($amount == $creditNote['amount'] && $creditNote['currency'] == $currency) {
                return;
            }
        }

        throw new \Exception('Not found');
    }
}
