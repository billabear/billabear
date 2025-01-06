<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Newsletter\EmailOctopus;

use BillaBear\Integrations\Newsletter\ListServiceInterface;
use BillaBear\Integrations\Newsletter\NewsletterList;
use GoranPopovic\EmailOctopus\Client;
use Parthenon\Common\LoggerAwareTrait;

class ListService implements ListServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private Client $client,
    ) {
    }

    public function getLists(): array
    {
        $this->getLogger()->info('Getting lists from EmailOctopus');
        $lists = $this->client->lists()->getAll();

        return array_map(fn ($list) => new NewsletterList((string) $list['id'], $list['name']), $lists['data']);
    }
}
