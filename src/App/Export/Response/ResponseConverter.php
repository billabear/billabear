<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Export\Response;

use Parthenon\Export\Exception\UnsupportedResponseTypeException;
use Parthenon\Export\ExportResponseInterface;
use Parthenon\Export\Response\DownloadResponse;
use Parthenon\Export\Response\EmailResponse;
use Parthenon\Export\Response\ResponseConverterInterface;
use Parthenon\Export\Response\WaitingResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ResponseConverter implements ResponseConverterInterface
{
    public function convert(ExportResponseInterface $exportResponse): Response
    {
        if ($exportResponse instanceof DownloadResponse) {
            return $exportResponse->getSymfonyResponse();
        } elseif ($exportResponse instanceof EmailResponse) {
            return new JsonResponse(['type' => 'email']);
        } elseif ($exportResponse instanceof WaitingResponse) {
            return new JsonResponse(['test' => 'wait', 'id' => $exportResponse->getId()]);
        }

        throw new UnsupportedResponseTypeException(sprintf("The response type '%s' is not supported", get_class($exportResponse)));
    }
}
