<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dev\DemoData;

use BillaBear\Command\DevDemoDataCommand;
use BillaBear\Customer\CustomerStatus;
use BillaBear\Customer\CustomerType;
use BillaBear\Customer\ExternalRegisterInterface;
use BillaBear\Entity\Customer;
use BillaBear\Payment\Provider\ProviderFactory;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Common\Address;
use Stripe\StripeClient;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerCreation
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private ExternalRegisterInterface $externalRegister,
        private ProviderFactory $providerFactory,
        private PaymentCardRepositoryInterface $paymentCardRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function createData(OutputInterface $output, bool $writeToStripe): void
    {
        /* @var StripeClient $stripe */
        if ($writeToStripe) {
            $stripe = new StripeClient($this->providerFactory->getApiKey());
        }

        $numberOfCustomers = DevDemoDataCommand::getNumberOfCustomers();
        $output->writeln('Starting to create customers');
        $progressBar = new ProgressBar($output, $numberOfCustomers);

        $progressBar->start();
        for ($i = 0; $i < $numberOfCustomers; ++$i) {
            $faker = Factory::create();
            $brandSettings = $this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);
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
            $customer->setBrandSettings($brandSettings);
            $customer->setCreatedAt(new \DateTime('now'));
            $customer->setType($faker->randomElement([CustomerType::BUSINESS, CustomerType::INDIVIDUAL]));

            if ($writeToStripe) {
                $this->externalRegister->register($customer);
            } else {
                $customer->setExternalCustomerReference('cus_'.$faker->text(7));
            }
            $this->customerRepository->save($customer);

            if ($writeToStripe) {
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
            } else {
                $paymentCard = new PaymentCard();
                $paymentCard->setLastFour($faker->numerify('####'));
                $paymentCard->setProvider('stripe');
                $paymentCard->setBrand($faker->randomElement(['visa', 'mastercard']));
                $paymentCard->setExpiryYear($faker->numberBetween(24, 55));
                $paymentCard->setExpiryMonth($faker->numberBetween(1, 12));
                $paymentCard->setStoredPaymentReference('card_'.$faker->text(7));
                $paymentCard->setStoredCustomerReference($customer->getExternalCustomerReference());
                $paymentCard->setCreatedAt(new \DateTime('now'));
                $paymentCard->setCustomer($customer);
            }

            $this->paymentCardRepository->save($paymentCard);
            $progressBar->advance();
            if (0 === $i % 100) {
                $this->entityManager->clear();
            }
        }
        $progressBar->finish();
    }
}
