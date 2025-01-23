<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Payment\ExchangeRates;

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
