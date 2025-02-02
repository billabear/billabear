<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Hubspot;

use BillaBear\Entity\Customer;
use BillaBear\Integrations\Crm\EmailServiceInterface;
use BillaBear\Notification\Email\Email;
use HubSpot\Client\Crm\Objects\Emails\ApiException;
use HubSpot\Client\Crm\Objects\Emails\Model\AssociationSpec;
use HubSpot\Client\Crm\Objects\Emails\Model\PublicAssociationsForObject;
use HubSpot\Client\Crm\Objects\Emails\Model\PublicObjectId;
use HubSpot\Client\Crm\Objects\Emails\Model\SimplePublicObjectInputForCreate;
use HubSpot\Discovery\Discovery;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Notification\Attachment;

class EmailService implements EmailServiceInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private Discovery $client,
    ) {
    }

    public function registerEmail(Customer $customer, Email $email): void
    {
        $this->logger->info('Registering email with Hubspot', [
            'customer' => (string) $customer->getId(),
        ]);

        $associations = [];
        $attachmentIds = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachmentIds[] = $this->uploadFile($attachment);
        }

        $associationSpec1 = new AssociationSpec([
            'association_category' => 'HUBSPOT_DEFINED',
            'association_type_id' => 198,
        ]);
        $to1 = new PublicObjectId([
            'id' => $customer->getCrmContactReference(),
        ]);
        $associations[] = new PublicAssociationsForObject([
            'types' => [$associationSpec1],
            'to' => $to1,
        ]);

        $associationSpec = new AssociationSpec([
            'association_category' => 'HUBSPOT_DEFINED',
            'association_type_id' => 186,
        ]);
        $to = new PublicObjectId([
            'id' => $customer->getCrmReference(),
        ]);
        $associations[] = new PublicAssociationsForObject([
            'types' => [$associationSpec],
            'to' => $to,
        ]);

        $properties1 = [
            'hs_email_status' => 'SENT',
            'hs_email_subject' => sprintf('BillaBear - %s', $email->getBillabearEmail()),
            'hs_email_direction' => 'EMAIL',
            'hs_timestamp' => time() * 1000,
            'hs_attachment_ids' => implode(',', $attachmentIds),
        ];
        $simplePublicObjectInputForCreate = new SimplePublicObjectInputForCreate([
            'associations' => $associations,
            'object_write_trace_id' => 'string',
            'properties' => $properties1,
        ]);
        try {
            $response = $this->client->crm()->objects()->emails()->basicApi()->create($simplePublicObjectInputForCreate);
        } catch (ApiException $e) {
            $this->getLogger()->error('Failed to register email with Hubspot', ['customer' => (string) $customer->getId(), 'error' => $e->getResponseBody()]);
            throw $e;
        }
    }

    protected function uploadFile(Attachment $attachment): string
    {
        $this->logger->info('Uploading attachment to Hubspot', [
            'attachment' => $attachment->getName(),
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'attachment');
        file_put_contents($tmpFile, $attachment->getContent());

        $splFile = new \SplFileObject($tmpFile);
        try {
            $response = $this->client->files()->filesApi()->upload($splFile, folder_path: 'billabear_attachments', file_name: $attachment->getName(), options: json_encode([
                'access' => 'PRIVATE',
                'ttl' => 'P2W',
                'overwrite' => false,
                'duplicateValidationStrategy' => 'NONE',
                'duplicateValidationScope' => 'EXACT_FOLDER',
            ]));
        } catch (\HubSpot\Client\Files\ApiException $e) {
            $this->getLogger()->error('Failed to register file with Hubspot', ['attachment' => $attachment->getName(), 'error' => $e->getResponseBody()]);
            throw $e;
        }

        return $response->getId();
    }
}
