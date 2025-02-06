<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Compliance;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use BillaBear\Logger\Audit\IndexProviderInterface;
use BillaBear\Tests\Behat\SendRequestTrait;
use Elastic\Elasticsearch\ClientInterface;

class AuditContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private ClientInterface $client,
        private IndexProviderInterface $indexProvider,
    ) {
    }

    #[Given('the following audit logs exist:')]
    public function theFollowingAuditLogsExist(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $data = [
                'message' => $row['Message'],
                'level' => 200,
                'level_name' => 'INFO',
                'channel' => 'audit',
                'datetime' => (new \DateTime($row['Created At']))->format(\DATE_ATOM),
                'context' => [
                ],
                'extra' => [
                ],
            ];
            $this->client->index([
                'index' => $this->indexProvider->getIndex(),
                'type' => 'elastic_doc_type',
                'body' => $data,
            ]);
        }
        sleep(1);
    }

    #[When('I go to the audit logs page')]
    public function iGoToTheAuditLogsPage(): void
    {
        $this->sendJsonRequest('GET', '/app/audit');
    }

    #[Then('I should see the audit log :arg1')]
    public function iShouldSeeTheAuditLog($message): void
    {
        $data = $this->getJsonContent();
        foreach ($data['data'] as $record) {
            if ($record['message'] === $message) {
                return;
            }
        }
        throw new \Exception("Unable to find audit log with message: $message");
    }
}
