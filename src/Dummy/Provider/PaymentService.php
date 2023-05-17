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

namespace App\Dummy\Provider;

use Obol\Model\BillingDetails;
use Obol\Model\CancelSubscription;
use Obol\Model\CardFile;
use Obol\Model\CardOnFileResponse;
use Obol\Model\Charge;
use Obol\Model\ChargeCardResponse;
use Obol\Model\FrontendCardProcess;
use Obol\Model\PaymentDetails;
use Obol\Model\Subscription;
use Obol\Model\SubscriptionCancellation;
use Obol\Model\SubscriptionCreationResponse;
use Obol\PaymentServiceInterface;

class PaymentService implements PaymentServiceInterface
{
    public function startSubscription(Subscription $subscription): SubscriptionCreationResponse
    {
        $paymentDetails = new PaymentDetails();
        $paymentDetails->setAmount($subscription->getTotalCost());
        $paymentDetails->setCustomerReference($subscription->getBillingDetails()->getCustomerReference());
        $paymentDetails->setStoredPaymentReference($subscription->getBillingDetails()->getStoredPaymentReference());
        $paymentDetails->setPaymentReference(bin2hex(random_bytes(32)));

        $subscriptionCreation = new SubscriptionCreationResponse();
        $subscriptionCreation->setSubscriptionId(bin2hex(random_bytes(32)));
        $subscriptionCreation->setBilledUntil(new \DateTime('+1 month'));
        $subscriptionCreation->setPaymentDetails($paymentDetails);
        $subscriptionCreation->setLineId(bin2hex(random_bytes(32)));

        return $subscriptionCreation;
    }

    public function stopSubscription(CancelSubscription $cancelSubscription): SubscriptionCancellation
    {
        $subscriptionCancellation = new SubscriptionCancellation();
        $subscriptionCancellation->setSubscription($cancelSubscription->getSubscription());

        return $subscriptionCancellation;
    }

    public function createCardOnFile(BillingDetails $billingDetails): CardOnFileResponse
    {
        $cardFile = new CardFile();
        $cardFile->setCustomerReference($billingDetails->getCustomerReference());
        $cardFile->setStoredPaymentReference(bin2hex(random_bytes(32)));
        $cardFile->setBrand('test');
        $cardFile->setLastFour('4242');
        $cardFile->setExpiryMonth('03');
        $cardFile->setExpiryYear('32');

        $cardOnFile = new CardOnFileResponse();
        $cardOnFile->setCardFile($cardFile);

        return $cardOnFile;
    }

    public function deleteCardFile(BillingDetails $cardFile): void
    {
    }

    public function chargeCardOnFile(Charge $cardFile): ChargeCardResponse
    {
        $paymentDetails = new PaymentDetails();
        $paymentDetails->setAmount($cardFile->getAmount());
        $paymentDetails->setCustomerReference($cardFile->getBillingDetails()->getCustomerReference());
        $paymentDetails->setStoredPaymentReference($cardFile->getBillingDetails()->getStoredPaymentReference());
        $paymentDetails->setPaymentReference(bin2hex(random_bytes(32)));

        $chargeCardResponse = new ChargeCardResponse();
        $chargeCardResponse->setPaymentDetails($paymentDetails);

        return $chargeCardResponse;
    }

    public function startFrontendCreateCardOnFile(BillingDetails $billingDetails): FrontendCardProcess
    {
        $frontendCardProcess = new FrontendCardProcess();
        $frontendCardProcess->setCustomerReference($billingDetails->getCustomerReference());
        $frontendCardProcess->setToken(bin2hex(random_bytes(32)));

        return $frontendCardProcess;
    }

    public function makeCardDefault(BillingDetails $billingDetails): void
    {
        // TODO: Implement makeCardDefault() method.
    }

    public function list(int $limit = 10, ?string $lastId = null): array
    {
        // TODO: Implement list() method.
    }
}
