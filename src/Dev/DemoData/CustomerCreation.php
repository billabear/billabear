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

namespace App\Dev\DemoData;

use App\Command\DevDemoDataCommand;
use App\Customer\ExternalRegisterInterface;
use App\Entity\Customer;
use App\Enum\CustomerStatus;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Common\Address;
use Stripe\StripeClient;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomerCreation
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private ExternalRegisterInterface $externalRegister,
        #[Autowire('%parthenon_billing_payments_obol_config%')]
        private $stripeConfig,
        private PaymentCardRepositoryInterface $paymentCardRepository,
    ) {
    }

    public function createData(OutputInterface $output): void
    {
        $faker = \Faker\Factory::create();
        /** @var StripeClient $stripe */
        $stripe = new StripeClient($this->stripeConfig['api_key']);

        $output->writeln('Starting to create customers');
        $progressBar = new ProgressBar($output, DevDemoDataCommand::NUMBER_OF_CUSTOMERS);

        $progressBar->start();
        for ($i = 0; $i < DevDemoDataCommand::NUMBER_OF_CUSTOMERS; ++$i) {
            $customer = new Customer();
            $customer->setBillingEmail($faker->email);
            $customer->setName($faker->name);

            $billingAddress = new Address();
            $billingAddress->setStreetLineOne($faker->streetAddress);
            $billingAddress->setCountry($faker->countryCode);
            $billingAddress->setCity($faker->city);
            $billingAddress->setPostcode($faker->postcode);
            $customer->setBillingAddress($billingAddress);
            $customer->setBillingType(Customer::BILLING_TYPE_CARD);
            $customer->setStatus(CustomerStatus::ACTIVE);
            $customer->setDisabled(false);
            $customer->setReference($faker->domainWord);
            $customer->setLocale($faker->locale);

            $this->externalRegister->register($customer);
            $this->customerRepository->save($customer);

            $stripeSource = $stripe->customers->createSource($customer->getExternalCustomerReference(), [
                'source' => 'tok_visa', // Test card token obtained from Stripe.js or Elements
            ]);

            $paymentCard = new PaymentCard();
            $paymentCard->setLastFour($stripeSource->last4);
            $paymentCard->setProvider('stripe');
            $paymentCard->setBrand($stripeSource->brand);
            $paymentCard->setExpiryYear($stripeSource->exp_year);
            $paymentCard->setExpiryMonth($stripeSource->exp_month);
            $paymentCard->setStoredPaymentReference($stripeSource->id);
            $paymentCard->setStoredCustomerReference($stripeSource->customer);
            $paymentCard->setCreatedAt(new \DateTime('now'));
            $paymentCard->setCustomer($customer);

            $this->paymentCardRepository->save($paymentCard);
            $progressBar->advance();
        }
        $progressBar->finish();
    }
}
