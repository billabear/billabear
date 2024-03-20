<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dummy\Provider;

use Obol\Exception\PaymentFailureException;
use Obol\Model\BillingDetails;
use Obol\Model\CancelSubscription;
use Obol\Model\CardFile;
use Obol\Model\CardOnFileResponse;
use Obol\Model\Charge;
use Obol\Model\ChargeCardResponse;
use Obol\Model\ChargeFailure;
use Obol\Model\Enum\ChargeFailureReasons;
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
        if ('ref_fails' === $subscription->getBillingDetails()->getStoredPaymentReference()) {
            throw new PaymentFailureException('Dummy failure');
        }
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
        if ('ref_fails' === $cardFile->getBillingDetails()->getStoredPaymentReference()) {
            $chargeCardResponse = new ChargeCardResponse();
            $chargeCardResponse->setSuccessful(false);
            $chargeCardResponse->setChargeFailure(new ChargeFailure());
            $chargeCardResponse->getChargeFailure()->setReason(ChargeFailureReasons::LACK_OF_FUNDS);

            return $chargeCardResponse;
        }

        $paymentDetails = new PaymentDetails();
        $paymentDetails->setAmount($cardFile->getAmount());
        $paymentDetails->setCustomerReference($cardFile->getBillingDetails()->getCustomerReference());
        $paymentDetails->setStoredPaymentReference($cardFile->getBillingDetails()->getStoredPaymentReference());
        $paymentDetails->setPaymentReference(bin2hex(random_bytes(32)));

        $chargeCardResponse = new ChargeCardResponse();
        $chargeCardResponse->setPaymentDetails($paymentDetails);
        $chargeCardResponse->setSuccessful(true);

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
