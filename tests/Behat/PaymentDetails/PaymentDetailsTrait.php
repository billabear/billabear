<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Tests\Behat\PaymentDetails;

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
