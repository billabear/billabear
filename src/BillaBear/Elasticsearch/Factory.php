<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class Factory
{
    public function __construct(
        #[Autowire(env: 'ELASTICSEARCH_HOST')]
        private string $host,
        #[Autowire(env: 'ELASTICSEARCH_PORT')]
        private string $port,
    ) {
    }

    public function build(): Client
    {
        $hosts = [
            sprintf('%s:%s', $this->host, $this->port),
        ];

        $elasticsearchClient = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();

        return $elasticsearchClient;
    }
}
