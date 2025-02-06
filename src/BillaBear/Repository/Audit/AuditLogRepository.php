<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Audit;

use BillaBear\DataMappers\BillingAdminDataMapper;
use BillaBear\Dto\Generic\App\AuditLog;
use BillaBear\Logger\Audit\IndexProviderInterface;
use BillaBear\Repository\UserRepositoryInterface;
use Elastic\Elasticsearch\ClientInterface;
use Parthenon\Athena\ResultSet;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function __construct(
        private IndexProviderInterface $indexProvider,
        private ClientInterface $client,
        private UserRepositoryInterface $userRepository,
        private BillingAdminDataMapper $billingAdminDataMapper,
    ) {
    }

    public function findAll(?string $lastId = null, int $limit = 25, bool $reverse = false): ResultSet
    {
        $body = [
            'query' => [
                'match_all' => (object) [],
            ],
            'sort' => [
                'datetime' => 'desc',
            ],
            'size' => $limit + 1,
        ];

        if (null !== $lastId) {
            $order = $reverse ? 'gt' : 'lt';
            $body['query'] = [
                'range' => [
                    'datetime' => [$order => $lastId],
                ],
            ];
        }

        $params = [
            'index' => [$this->indexProvider->getIndex()],
            'body' => $body,
        ];

        $response = $this->client->search($params);
        $hits = $response['hits']['hits'];
        $data = [];

        foreach ($hits as $hit) {
            $data[] = $this->buildLog($hit['_source']);
        }

        $result = new ResultSet(
            results: $data,
            sortKey: 'createdAt',
            sortType: 'desc',
            limit: $limit,
        );

        return $result;
    }

    private function buildLog(array $data): AuditLog
    {
        $billingAdminDto = null;
        if (isset($data['extra']['billing_admin_id'])) {
            $admin = $this->userRepository->getById($data['extra']['billing_admin_id'], true);
            $billingAdminDto = $this->billingAdminDataMapper->createAppDto($admin);
        }

        return new AuditLog(
            message: $data['message'],
            type: $data['channel'],
            createdAt: new \DateTime($data['datetime']),
            context: $data['context'],
            billingAdmin: $billingAdminDto,
        );
    }
}
