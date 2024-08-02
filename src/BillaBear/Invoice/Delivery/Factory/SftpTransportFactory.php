<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Delivery\Factory;

use BillaBear\Entity\InvoiceDeliverySettings;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

class SftpTransportFactory
{
    public function buildTransport(InvoiceDeliverySettings $invoiceDelivery): FilesystemOperator
    {
        return new Filesystem(new SftpAdapter(
            new SftpConnectionProvider(
                $invoiceDelivery->getSftpHost(),
                $invoiceDelivery->getSftpUser(),
                $invoiceDelivery->getSftpPassword(),
                port: $invoiceDelivery->getSftpPort(),
            ),
            $invoiceDelivery->getSftpDir(),
            PortableVisibilityConverter::fromArray([
                'file' => [
                    'public' => 0640,
                    'private' => 0604,
                ],
                'dir' => [
                    'public' => 0740,
                    'private' => 7604,
                ],
            ])
        ));
    }
}
