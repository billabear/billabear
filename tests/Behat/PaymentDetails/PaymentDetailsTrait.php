<?php

/*
 * Copyright all rights reserved. No public license given.
 */

namespace BillaBear\Tests\Behat\PaymentDetails;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;

trait PaymentDetailsTrait
{
    public function findPaymentMethod(CustomerInterface $customer, string $paymentDetailsName): PaymentCard
    {
        $paymentDetails = $this->paymentDetailsRepository->findOneBy(['customer' => $customer, 'name' => $paymentDetailsName]);

        if (!$paymentDetails instanceof PaymentCard) {
            throw new \Exception('No payment details found');
        }

        $this->paymentDetailsRepository->getEntityManager()->refresh($paymentDetails);

        return $paymentDetails;
    }
}
