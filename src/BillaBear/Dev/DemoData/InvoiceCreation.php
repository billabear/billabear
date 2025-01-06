<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dev\DemoData;

use BillaBear\Command\DevDemoDataCommand;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\Payment;
use BillaBear\Event\Invoice\InvoicePaid;
use BillaBear\Invoice\InvoiceGenerator;
use BillaBear\Payment\InvoiceCharger;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\Schedule\SchedulerProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Factory;
use Obol\Exception\PaymentFailureException;
use Obol\Model\PaymentDetails;
use Parthenon\Athena\Filters\GreaterThanFilter;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Event\PaymentCreated;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InvoiceCreation
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private InvoiceGenerator $invoiceGenerator,
        private InvoiceCharger $invoiceCharger,
        private SchedulerProvider $schedulerProvider,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private PaymentFactoryInterface $paymentFactory,
        private PaymentRepositoryInterface $paymentRepository,
        private EventDispatcherInterface $eventDispatcher,
        private InvoiceRepositoryInterface $invoiceRepository,
    ) {
    }

    public function createData(OutputInterface $output, bool $writeToStripe): void
    {
        $output->writeln("\nCreating invoices");

        $lastId = null;
        $limit = 25;
        $now = new \DateTime('now');
        $startDate = DevDemoDataCommand::getStartDate();
        $brand = $this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);

        $progressBar = new ProgressBar($output, $this->subscriptionRepository->getCreatedCountForPeriod($startDate, $now, $brand));
        $progressBar->start();
        do {
            $filter = new GreaterThanFilter();
            $filter->setFieldName('createdAt');
            $filter->setData($startDate);

            $filters = [$filter];
            $result = $this->subscriptionRepository->getList(filters: $filters, limit: $limit, lastId: $lastId);
            $data = $result->getResults();
            $lastId = $result->getLastKey();

            /** @var Subscription $subscription */
            foreach ($data as $subscription) {
                $progressBar->advance();
                if ($subscription->getValidUntil() > $now) {
                    continue;
                }
                do {
                    $lastStart = clone $subscription->getValidUntil();
                    $lastStart->modify('+1 minute');
                    $subscription->setStartOfCurrentPeriod($lastStart);
                    $this->schedulerProvider->getScheduler($subscription->getPrice())->scheduleNextDueDate($subscription);
                    $subscription->setUpdatedAt(new \DateTime('now'));
                    $invoice = $this->invoiceGenerator->generateForCustomerAndSubscriptions($subscription->getCustomer(), [$subscription]);
                    try {
                        if ($writeToStripe) {
                            $this->invoiceCharger->chargeInvoice($invoice, createdAt: $subscription->getStartOfCurrentPeriod());
                        } else {
                            $this->createPaymentLocally($invoice, $subscription, $lastStart);
                        }
                    } catch (PaymentFailureException) {
                    }
                } while ($subscription->getValidUntil() < $now);
            }
        } while (!empty($data));
        $progressBar->finish();
    }

    public function createPaymentLocally(Invoice $invoice, Subscription $subscription, \DateTime $lastStart): void
    {
        $faker = Factory::create();
        $paymentDetails = new PaymentDetails();
        $paymentDetails->setPaymentReference('ch_'.$faker->text(8));
        $paymentDetails->setPaymentReferenceLink($faker->url());
        $paymentDetails->setAmount($invoice->getTotalMoney());

        /** @var Payment $payment */
        $payment = $this->paymentFactory->fromSubscriptionCreation($paymentDetails, $invoice->getCustomer());

        $invoice->setPaidAt($lastStart);

        $payment->addSubscription($subscription);
        $payment->setInvoice($invoice);

        $this->paymentRepository->save($payment);
        $invoice->setPayments(new ArrayCollection([$payment]));
        $invoice->setPaid(true);
        $this->invoiceRepository->save($invoice);

        $this->eventDispatcher->dispatch(new InvoicePaid($invoice), InvoicePaid::NAME);
        $this->eventDispatcher->dispatch(new PaymentCreated($payment, true), PaymentCreated::NAME);
    }
}
