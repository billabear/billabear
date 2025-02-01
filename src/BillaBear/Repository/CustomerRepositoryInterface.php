<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use Parthenon\Common\Exception\NoEntityFoundException;

interface CustomerRepositoryInterface extends \Parthenon\Billing\Repository\CustomerRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findByEmail(string $email): Customer;

    public function hasCustomerByEmail(string $email): bool;

    public function getOldestCustomer(): Customer;

    public function getCreatedCountForPeriod(\DateTime $startDate, \DateTime $endDate, BrandSettings $brandSettings): int;

    public function getTotalCount(): int;

    public function getLatestCustomers(int $number = 10): array;

    public function wipeCustomerSupportReferences(): void;

    public function wipeCrmReferences(): void;

    public function wipeAccountingReferences(): void;

    public function wipeNewsletterReferences(): void;
}
