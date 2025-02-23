<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax\Report;

use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\Tax\TaxTypeDataMapper;
use BillaBear\Dto\Generic\App\TaxReportItem;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;

class ReportItemBuilder
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerDataMapper $customerDataMapper,
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private TaxTypeDataMapper $taxTypeDataMapper,
    ) {
    }

    public function buildItem(array $rawData): TaxReportItem
    {
        $customer = $this->customerRepository->findById($rawData['customer_id']);
        $taxType = $this->taxTypeRepository->findById($rawData['tax_type_id']);

        $item = new TaxReportItem();
        $item->setCustomer($this->customerDataMapper->createAppDto($customer));
        $item->setTaxType($this->taxTypeDataMapper->createAppDto($taxType));

        return $item;
    }
}
