<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
