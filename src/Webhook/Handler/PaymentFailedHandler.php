<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Webhook\Handler;

use Obol\Model\Events\ChargeFailed;
use Obol\Model\Events\ChargeSucceeded;
use Obol\Model\Events\EventInterface;
use Parthenon\Billing\Customer\CustomerManagerInterface;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Event\PaymentCreated;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Subscription\PaymentEventLinkerInterface;
use Parthenon\Billing\Webhook\HandlerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PaymentFailedHandler implements HandlerInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private CustomerManagerInterface $customerManager,
        private PaymentFactoryInterface $paymentFactory,
        private PaymentEventLinkerInterface $eventLinker,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function supports(EventInterface $event): bool
    {
        return $event instanceof ChargeFailed;
    }

    /**
     * @param ChargeSucceeded $event
     */
    public function handle(EventInterface $event): void
    {
        try {
            $payment = $this->paymentRepository->getPaymentForReference($event->getPaymentReference());
        } catch (NoEntityFoundException $exception) {
            $payment = $this->paymentFactory->fromChargeEvent($event);
        }
        $payment->setStatus(PaymentStatus::FAILED);
        $payment->setUpdatedAt(new \DateTime('now'));

        if ($event->hasExternalCustomerId()) {
            try {
                $customer = $this->customerManager->getCustomerForReference($event->getExternalCustomerId());
                $payment->setCustomer($customer);
            } catch (NoCustomerException $e) {
                // Handle error some how.
            }
        }

        $this->eventLinker->linkToSubscription($payment, $event);

        $this->paymentRepository->save($payment);
        $this->dispatcher->dispatch(new PaymentCreated($payment), PaymentCreated::NAME);
    }
}
