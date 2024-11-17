<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tax\VatSense;

use BillaBear\Exception\Tax\VatSense\FailedRequestException;
use BillaBear\Kernel;
use BillaBear\Repository\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Nyholm\Psr7\Request;
use Parthenon\Common\LoggerAwareTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class VatSenseClient
{
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository)
    {
    }

    /**
     * @throws FailedRequestException
     */
    public function validateTaxId(string $taxId): bool
    {
        $url = sprintf('https://api.vatsense.com/1.0/validate?vat_number=%s', $taxId);
        $request = new Request('GET', $url);
        $request = $request->withAddedHeader('User-Agent', 'BillaBear/'.Kernel::VERSION_ID);
        $response = $this->sendRequest($request);
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['data']['valid'];
    }

    public function getTaxRates(): array
    {
        $url = sprintf('https://api.vatsense.com/1.0/rates');
        $request = new Request('GET', $url);
        $response = $this->sendRequest($request);
        $data = json_decode($response->getBody()->getContents(), true);

        return $data['data'];
    }

    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->getLogger()->info("Sending request to VatSense's API", ['url' => $request->getUri()]);
        $client = new Client([
            'auth' => ['user', $this->settingsRepository->getDefaultSettings()->getTaxSettings()->getVatSenseApiKey()],
        ]);
        try {
            $response = $client->send($request);
            $this->getLogger()->info("Response received from VatSense's API", ['status' => $response->getStatusCode()]);
        } catch (BadResponseException $e) {
            $this->getLogger()->warning("Received an error from VatSense's API", ['status' => $e->getResponse()->getStatusCode(), 'body' => $e->getResponse()->getBody()->getContents()]);

            throw new FailedRequestException('Got a bad response', previous: $e);
        }

        return $response;
    }
}
