<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class SystemSettings
{
    #[Assert\NotBlank]
    #[Assert\Url]
    #[SerializedName('system_url')]
    private $systemUrl;

    #[Assert\NotBlank]
    #[Assert\Timezone]
    private $timezone;

    public function getSystemUrl()
    {
        return $this->systemUrl;
    }

    public function setSystemUrl($systemUrl): void
    {
        $this->systemUrl = $systemUrl;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getInvoiceNumberGeneration()
    {
        return $this->invoiceNumberGeneration;
    }

    public function setInvoiceNumberGeneration($invoiceNumberGeneration): void
    {
        $this->invoiceNumberGeneration = $invoiceNumberGeneration;
    }

    public function getSubsequentialNumber()
    {
        return $this->subsequentialNumber;
    }

    public function setSubsequentialNumber($subsequentialNumber): void
    {
        $this->subsequentialNumber = $subsequentialNumber;
    }

    public function getDefaultInvoiceDueTime()
    {
        return $this->defaultInvoiceDueTime;
    }

    public function setDefaultInvoiceDueTime($defaultInvoiceDueTime): void
    {
        $this->defaultInvoiceDueTime = $defaultInvoiceDueTime;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format): void
    {
        $this->format = $format;
    }
}
