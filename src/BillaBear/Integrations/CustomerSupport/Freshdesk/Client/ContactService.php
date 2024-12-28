<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\CustomerSupport\Freshdesk\Client;

use BillaBear\Exception\Integrations\UnexpectedErrorException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use Parthenon\Common\LoggerAwareTrait;

class ContactService
{
    use LoggerAwareTrait;

    private ClientInterface $client;

    public function __construct(
        private string $apiKey,
        private string $subdomain,
        ?ClientInterface $client = null,
    ) {
        $this->client = $client ?? new \GuzzleHttp\Client();
    }

    public function create(array $customer): array
    {
        return $this->sendRequest('/contacts', 'POST', $customer);
    }

    public function update(int|string $id, array $customer): array
    {
        return $this->sendRequest(sprintf('/contacts/%d', $id), 'PUT', $customer);
    }

    public function all(?array $query = null): array
    {
        $endpoint = '/contacts';

        if ($query) {
            $endpoint .= '?'.http_build_query($query);
        }

        return $this->sendRequest($endpoint);
    }

    public function createCustomField(array $customField): array
    {
        return $this->sendRequest('/contact_fields', 'POST', $customField);
    }

    public function allCustomFields(): array
    {
        $endpoint = '/contact_fields';

        return $this->sendRequest($endpoint);
    }

    private function buildUrl(string $endpoint): string
    {
        return sprintf('https://%s.freshdesk.com/api/v2/%s', $this->subdomain, $endpoint);
    }

    private function sendRequest($endpoint, string $method = 'GET', ?array $body = null): array
    {
        $url = $this->buildUrl($endpoint);

        $request = new Request($method, $url, [
            'Authorization' => 'Basic '.base64_encode(sprintf('%s:X', $this->apiKey)),
            'Content-Type' => 'application/json',
        ]);
        if ($body) {
            $resource = fopen('php://temp', 'r+');
            fwrite($resource, json_encode($body));
            rewind($resource);
            $stream = new Stream($resource);
            $request = $request->withBody($stream);
        }

        try {
            $this->getLogger()->info('Sending request to Freshdesk', ['url' => $url, 'method' => $method, 'body' => json_encode($body)]);
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $this->getLogger()->info('Received an error with request to Freshdesk', [
                'status_code' => $response->getStatusCode(),
                'url' => $url,
                'method' => $method,
                'body' => $response->getBody()->getContents(),
            ]);

            throw new UnexpectedErrorException('An unexpected error occurred while communicating with Freshdesk', previous: $e);
        } catch (\Exception $e) {
            $this->getLogger()->error('An error occurred while communicating with Freshdesk', ['message' => $e->getMessage()]);

            throw new UnexpectedErrorException('An unexpected error occurred while communicating with Freshdesk', previous: $e);
        }
        $this->getLogger()->info('Received response from Freshdesk', ['status_code' => $response->getStatusCode(), 'body' => $response->getBody()->getContents()]);
        $response->getBody()->rewind();

        return json_decode($response->getBody()->getContents(), true);
    }
}
