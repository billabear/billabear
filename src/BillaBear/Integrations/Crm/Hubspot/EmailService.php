<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Hubspot;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Crm\EmailServiceInterface;
use HubSpot\Client\Crm\Objects\Emails\ApiException;
use HubSpot\Client\Crm\Objects\Emails\Model\AssociationSpec;
use HubSpot\Client\Crm\Objects\Emails\Model\PublicAssociationsForObject;
use HubSpot\Client\Crm\Objects\Emails\Model\PublicObjectId;
use HubSpot\Client\Crm\Objects\Emails\Model\SimplePublicObjectInputForCreate;
use HubSpot\Discovery\Discovery;
use Parthenon\Common\LoggerAwareTrait;

class EmailService implements EmailServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private Discovery $client,
    ) {
    }

    public function registerEmail(Customer $customer, string $templateName): void
    {
        $this->logger->info('Registering email with Hubspot', [
            'customer' => (string) $customer->getId(),
        ]);

        $associationSpec1 = new AssociationSpec([
            'association_category' => 'HUBSPOT_DEFINED',
            'association_type_id' => 198,
        ]);
        $to1 = new PublicObjectId([
            'id' => $customer->getCrmContactReference(),
        ]);

        $associationSpec2 = new AssociationSpec([
            'association_category' => 'HUBSPOT_DEFINED',
            'association_type_id' => 186,
        ]);
        $to2 = new PublicObjectId([
            'id' => $customer->getCrmReference(),
        ]);

        $publicAssociationsForObject1 = new PublicAssociationsForObject([
            'types' => [$associationSpec1],
            'to' => $to1,
        ]);
        $publicAssociationsForObject2 = new PublicAssociationsForObject([
            'types' => [$associationSpec2],
            'to' => $to2,
        ]);
        $properties1 = [
            'hs_email_status' => 'SENT',
            'hs_email_subject' => sprintf('BillaBear - %s', $templateName),
            'hs_email_direction' => 'EMAIL',
            'hs_timestamp' => time() * 1000,
        ];
        $simplePublicObjectInputForCreate = new SimplePublicObjectInputForCreate([
            'associations' => [$publicAssociationsForObject1, $publicAssociationsForObject2],
            'object_write_trace_id' => 'string',
            'properties' => $properties1,
        ]);
        try {
            $this->client->crm()->objects()->emails()->basicApi()->create($simplePublicObjectInputForCreate);
        } catch (ApiException $e) {
            $this->getLogger()->error('Failed to register email with Hubspot', ['customer' => (string) $customer->getId(), 'error' => $e->getResponseBody()]);
        }
    }
}
