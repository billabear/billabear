<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\Api;

use BillaBear\Dto\Generic\Address;
use Symfony\Component\Serializer\Annotation\SerializedName;

readonly class Invoice
{
    public function __construct(
        public string $id,
        public string $number,
        public string $currency,
        public int $total,
        #[SerializedName('tax_total')]
        public int $taxTotal,
        #[SerializedName('sub_total')]
        public int $subTotal,
        #[SerializedName('amount_due')]
        public int $amountDue,
        #[SerializedName('is_paid')]
        public bool $isPaid,
        #[SerializedName('paid_at')]
        public ?\DateTimeInterface $paidAt,
        #[SerializedName('created_at')]
        public \DateTime $createdAt,
        public Customer $customer,
        #[SerializedName('biller_address')]
        public Address $billerAddress,
        #[SerializedName('payee_address')]
        public Address $payeeAddress,
        public array $lines,
        #[SerializedName('pay_link')]
        public string $payLink,
        #[SerializedName('due_date')]
        public ?\DateTime $dueDate,
    ) {
    }
}
