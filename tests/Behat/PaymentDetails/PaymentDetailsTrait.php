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

namespace App\Tests\Behat\PaymentDetails;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentDetails;

trait PaymentDetailsTrait
{
    public function findPaymentDetails(CustomerInterface $customer, string $paymentDetailsName): PaymentDetails
    {
        $paymentDetails = $this->paymentDetailsRepository->findOneBy(['customer' => $customer, 'name' => $paymentDetailsName]);

        if (!$paymentDetails instanceof PaymentDetails) {
            throw new \Exception('No payment details found');
        }

        $this->paymentDetailsRepository->getEntityManager()->refresh($paymentDetails);

        return $paymentDetails;
    }
}
