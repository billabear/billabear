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

namespace App\Tests\Behat;

trait SendRequestTrait
{
    public static ?string $apiKey = null;

    protected function authenticate(?string $apiKey): void
    {
        ApiKeyHelper::$apiKey = $apiKey;
    }

    protected function isStripe(bool $isStripe): void
    {
        ApiKeyHelper::$stripe = $isStripe;
    }

    protected function sendJsonRequest(string $method, string $url, array $body = []): void
    {
        $jsonBody = json_encode($body);
        $this->session->visit('/');
        $components = parse_url($this->session->getCurrentUrl());
        $baseUrl = $components['scheme'].'://'.$components['host'].$url;
        $headers = [
            'Accept' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];

        if (isset(ApiKeyHelper::$apiKey)) {
            $headers['HTTP_X-API-KEY'] = ApiKeyHelper::$apiKey;
        }

        $client = $this->session->getDriver()->getClient();
        $client->request(
            $method,
            $baseUrl,
            [],
            [],
            $headers,
            $jsonBody);

        if (500 === $this->session->getStatusCode()) {
            throw new \Exception('Got '.$this->session->getPage()->getContent());
        }
    }

    protected function getJsonContent(): array
    {
        $content = $this->session->getPage()->getContent();
        $json = json_decode($content, true);
        if (!$json) {
            throw new \Exception(sprintf('No valid JSON found. Got status code %s', $this->session->getStatusCode()));
        }

        return $json;
    }
}
