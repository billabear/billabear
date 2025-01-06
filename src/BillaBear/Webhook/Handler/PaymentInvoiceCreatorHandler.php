<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Webhook\Handler;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Payment;
use BillaBear\Invoice\InvoiceGenerator;
use BillaBear\Invoice\LineItem;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\InvoiceRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use Obol\Exception\UnsupportedFunctionalityException;
use Obol\Model\Events\ChargeSucceeded;
use Obol\Model\Events\EventInterface;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Customer\CustomerManagerInterface;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Subscription\PaymentLinkerInterface;
use Parthenon\Billing\Subscription\SchedulerInterface;
use Parthenon\Billing\Webhook\HandlerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentInvoiceCreatorHandler implements HandlerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private CustomerManagerInterface $customerManager,
        private PaymentFactoryInterface $paymentFactory,
        private PaymentLinkerInterface $eventLinker,
        private EventDispatcherInterface $dispatcher,
        private SchedulerInterface $scheduler,
        private InvoiceGenerator $invoiceGenerator,
        private ProviderInterface $provider,
        private CustomerDataMapper $customerDataMapper,
        private CustomerRepositoryInterface $customerRepository,
        private InvoiceRepositoryInterface $invoiceRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
    ) {
    }

    public function supports(EventInterface $event): bool
    {
        return $event instanceof ChargeSucceeded;
    }

    /**
     * @param ChargeSucceeded $event
     *
     * @throws UnsupportedFunctionalityException
     */
    public function handle(EventInterface $event): void
    {
        try {
            /** @var Payment $payment */
            $payment = $this->paymentRepository->getPaymentForReference($event->getPaymentReference());
            $this->getLogger()->info('Found payment in database', ['payment_reference' => $event->getPaymentReference()]);
        } catch (NoEntityFoundException $exception) {
            /** @var Payment $payment */
            $payment = $this->paymentFactory->fromChargeEvent($event);
            $this->getLogger()->info('Creating payment', ['payment_reference' => $event->getPaymentReference()]);
        }

        if ($payment->getInvoice()) {
            return;
        }

        $payment->setStatus(PaymentStatus::COMPLETED);
        $payment->setUpdatedAt(new \DateTime('now'));

        if ($event->hasExternalCustomerId()) {
            try {
                $customer = $this->customerManager->getCustomerForReference($event->getExternalCustomerId());
                $payment->setCustomer($customer);
                $this->getLogger()->info('Found customer');
            } catch (NoCustomerException $e) {
                $this->getLogger()->warning('No customer found. Importing them now.', ['external_customer_id' => $event->getExternalCustomerId()]);
                $customerModel = $this->provider->customers()->fetch($event->getExternalCustomerId());
                $customer = $this->customerDataMapper->createCustomerFromObol($customerModel);
                $brandSettings = $this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);
                $customer->setBrandSettings($brandSettings);
                $this->customerRepository->save($customer);
                $payment->setCustomer($customer);
            }
        }

        $this->eventLinker->linkToSubscription($payment, $event);

        $this->paymentRepository->save($payment);
        if (isset($customer)) {
            $subscriptions = $payment->getSubscriptions()->toArray();
            $lineItems = [];
            if (empty($subscriptions)) {
                $defaultTaxType = $this->taxTypeRepository->getDefault();
                $line = new LineItem();
                $line->setMoney($payment->getMoneyAmount());
                $line->setDescription($payment->getDescription() ?? '');
                $line->setIncludeTax(true);
                $line->setTaxType($defaultTaxType);
                $lineItems[] = $line;
            }

            $this->invoiceGenerator->generateForCustomerAndSubscriptions($customer, $subscriptions, $lineItems);
        }
    }
}
