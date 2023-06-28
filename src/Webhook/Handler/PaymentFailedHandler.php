<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Webhook\Handler;

use App\Payment\PaymentFailureHandler;
use Obol\Model\Events\ChargeFailed;
use Obol\Model\Events\EventInterface;
use Parthenon\Billing\Customer\CustomerManagerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Subscription\PaymentLinkerInterface;
use Parthenon\Billing\Webhook\HandlerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;

class PaymentFailedHandler implements HandlerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private CustomerManagerInterface $customerManager,
        private PaymentFactoryInterface $paymentFactory,
        private PaymentLinkerInterface $eventLinker,
        private PaymentFailureHandler $paymentFailureHandler,
    ) {
    }

    public function supports(EventInterface $event): bool
    {
        return $event instanceof ChargeFailed;
    }

    /**
     * @param ChargeFailed $event
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
                $this->getLogger()->emergency('No customer found for payment while customer reference given. Possibly an import is neeeded.');
            }
        }

        $this->eventLinker->linkToSubscription($payment, $event);

        /** @var Subscription $subscription */
        foreach ($payment->getSubscriptions() as $subscription) {
            $subscription->setStatus(SubscriptionStatus::OVERDUE_PAYMENT_OPEN);
        }
        // Todo remove the above.
        $this->paymentFailureHandler->handlePayment($payment, $event);
    }
}
