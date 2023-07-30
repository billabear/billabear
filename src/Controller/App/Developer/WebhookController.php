<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Developer;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\Developer\WebhookEndpointDataMapper;
use App\Dto\Request\App\Developer\Webhook\CreateWebhookEndpoint;
use App\Repository\WebhookEndpointRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WebhookController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/developer/webhook', name: 'app_app_developer_webhook_createwebhook', methods: ['POST'])]
    public function createWebhook(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        WebhookEndpointDataMapper $webhookEndpointDataMapper,
        WebhookEndpointRepositoryInterface $repository,
    ): Response {
        $dto = $serializer->deserialize($request->getContent(), CreateWebhookEndpoint::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $webhookEndpointDataMapper->createEntity($dto);
        $repository->save($entity);
        $outputDto = $webhookEndpointDataMapper->createAppDto($entity);
        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
