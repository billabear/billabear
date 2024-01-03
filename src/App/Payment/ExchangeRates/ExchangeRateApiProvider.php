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

namespace App\Payment\ExchangeRates;

use Parthenon\Common\Http\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * Uses https://www.exchangerate-api.com/docs/free.
 */
class ExchangeRateApiProvider implements ProviderInterface
{
    private const URL_TEMPLATE = 'https://open.er-api.com/v6/latest/%s';

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    public function getRates(string $currencyCode): array
    {
        $request = $this->requestFactory->createRequest('GET', sprintf(self::URL_TEMPLATE, $currencyCode));
        $response = $this->client->sendRequest($request);
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['rates'];
    }
}
