<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Compliance;

use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Repository\Audit\AuditLogRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuditLogController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/audit', name: 'app_compliance_audit', methods: ['GET'])]
    public function listAction(
        Request $request,
        AuditLogRepositoryInterface $auditLogRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Listing audit logs');

        $result = $auditLogRepository->findAll();

        $crudView = new ListResponse();
        $crudView->setData($result->getResults());
        $crudView->setLastKey($result->getLastKey());
        $crudView->setFirstKey($result->getFirstKey());
        $crudView->setHasMore($result->hasMore());

        $json = $serializer->serialize($crudView, 'json');

        return new JsonResponse($json, 200, [], true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
