<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Accounting;

use BillaBear\Exception\Integrations\MissingConfigurationException;
use BillaBear\Exception\Integrations\UnsupportedFeatureException;

interface AccountingIntegrationInterface
{
    /**
     * @throws UnsupportedFeatureException
     * @throws MissingConfigurationException
     */
    public function getInvoiceService(): InvoiceServiceInterface;

    /**
     * @throws UnsupportedFeatureException
     * @throws MissingConfigurationException
     */
    public function getCustomerService(): CustomerServiceInterface;

    /**
     * @throws UnsupportedFeatureException
     * @throws MissingConfigurationException
     */
    public function getPaymentService(): PaymentServiceInterface;

    /**
     * @throws UnsupportedFeatureException
     * @throws MissingConfigurationException
     */
    public function getCreditService(): CreditServiceInterface;
}
