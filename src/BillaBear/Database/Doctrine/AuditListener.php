<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Database\Doctrine;

use BillaBear\Logger\Audit\AuditableInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Psr\Log\LoggerInterface;

class AuditListener
{
    public function __construct(
        private readonly LoggerInterface $auditLogger,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof AuditableInterface) {
                continue;
            }

            $changeSet = $uow->getEntityChangeSet($entity);

            if (!empty($changeSet)) {
                foreach ($changeSet as $field => $change) {
                    $oldValue = $change[0];
                    $newValue = $change[1];

                    // Handle different data types appropriately (e.g., dates, objects)
                    $oldValueStr = $this->formatValue($oldValue);
                    $newValueStr = $this->formatValue($newValue);

                    $context = [
                        $entity->getAuditLogIdTag() => (string) $entity->getId(),
                        'field' => $field,
                        'old_value' => $oldValueStr,
                        'new_value' => $newValueStr,
                    ];

                    $this->auditLogger->info(sprintf('A field on the %s entity has been updated', $entity->getAuditName()), $context);
                }
            }
        }
    }

    private function formatValue($value)
    {
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        } elseif (is_object($value) && $value instanceof \DateTimeInterface) {
            return $value->format(\DateTime::ATOM);
        } elseif (is_array($value)) {
            return var_export($value, true);
        } elseif (is_resource($value)) {
            return 'Resource';
        } elseif (is_object($value) && method_exists($value, 'getId')) {
            return (string) $value->getId();
        } elseif (is_object($value)) {
            return get_class($value);
        } else {
            return var_export($value, true);
        }
    }
}
