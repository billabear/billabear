<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\CreditNote;

use App\Entity\CreditNote;
use App\Repository\Orm\CreditNoteRepository;
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
        private CreditNoteRepository $creditNoteRepository,
        private CustomerRepository $customerRepository,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @When I create a credit note for :arg1 for :arg3 in the currency :arg2
     */
    public function iCreateACreditNoteForForInTheCurrency($customerEmail, $amount, $currency)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $payload = [
            'amount' => $amount,
            'currency' => $currency,
        ];

        $this->sendJsonRequest('POST', '/app/customer/'.$customer->getId().'/credit-note', $payload);
    }

    /**
     * @Then there should be a credit note for :arg1 for :arg3 in the currency :arg2
     */
    public function thereShouldBeACreditNoteForForInTheCurrency($customerEmail, $amount, $currency)
    {
        $customer = $this->getCustomerByEmail($customerEmail);

        $creditNote = $this->creditNoteRepository->findOneBy(['customer' => $customer]);

        if (!$creditNote instanceof CreditNote) {
            throw new \Exception('No credit note found');
        }

        if ($creditNote->getCurrency() != $currency || $creditNote->getAmount() != $amount) {
            throw new \Exception('Wrong currency or amount');
        }
    }

    /**
     * @Then there should be a credit note created by :arg1
     */
    public function thereShouldBeACreditNoteCreatedBy($email)
    {
        $billingAdmin = $this->userRepository->findOneBy(['email' => $email]);

        $creditNote = $this->creditNoteRepository->findOneBy(['billingAdmin' => $billingAdmin]);

        if (!$creditNote instanceof CreditNote) {
            throw new \Exception('No credit note found');
        }
    }

    /**
     * @When the following credit notes exist:
     */
    public function theFollowingCreditNotesExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $customer = $this->getCustomerByEmail($row['Customer']);
            $creditNote = new CreditNote();
            $creditNote->setCustomer($customer);
            $creditNote->setAmount(intval($row['Amount']));
            $creditNote->setCurrency($row['Currency']);
            $creditNote->setUsedAmount(intval($row['Used Amount'] ?? 0));
            $creditNote->setCompletelyUsed($creditNote->getUsedAmount() === $creditNote->getAmount());
            $creditNote->setCreatedAt(new \DateTime());
            $creditNote->setUpdatedAt(new \DateTime());
            $creditNote->setCreationType(CreditNote::CREATION_TYPE_AUTOMATED);

            $this->creditNoteRepository->getEntityManager()->persist($creditNote);
        }

        $this->creditNoteRepository->getEntityManager()->flush();
    }

    /**
     * @Then I will see a credit note for :arg2 in the currency :arg1
     */
    public function iWillSeeACreditNoteForInTheCurrency($amount, $currency)
    {
        $data = $this->getJsonContent();

        if (!isset($data['credit_notes'])) {
            throw new \Exception('No credit notes');
        }

        foreach ($data['credit_notes'] as $creditNote) {
            if ($amount == $creditNote['amount'] && $creditNote['currency'] == $currency) {
                return;
            }
        }

        throw new \Exception('Not found');
    }
}
