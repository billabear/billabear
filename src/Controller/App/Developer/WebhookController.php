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
use App\DataMappers\Developer\WebhookEventDataMapper;
use App\DataMappers\Developer\WebhookEventResponseDataMapper;
use App\Dto\Request\App\Developer\Webhook\CreateWebhookEndpoint;
use App\Dto\Response\App\Developer\Webhook\ViewWebhookEndpoint;
use App\Dto\Response\App\Developer\Webhook\ViewWebhookEvent;
use App\Dto\Response\App\ListResponse;
use App\Entity\WebhookEvent;
use App\Repository\WebhookEndpointRepositoryInterface;
use App\Repository\WebhookEventRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
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

    #[Route('/app/developer/webhook', name: 'app_app_developer_webhook_listwebhooks', methods: ['GET'])]
    public function listWebhooks(
        Request $request,
        WebhookEndpointRepositoryInterface $repository,
        SerializerInterface $serializer,
        WebhookEndpointDataMapper $factory,
    ): Response {
        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $resultsPerPage = (int) $request->get('per_page', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $resultSet = $repository->getList(
            filters: [],
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$factory, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/developer/webhook/{id}/view', name: 'app_app_developer_webhook_view_webhooks', methods: ['GET'])]
    public function viewWebhooks(
        Request $request,
        WebhookEndpointRepositoryInterface $repository,
        SerializerInterface $serializer,
        WebhookEndpointDataMapper $factory,
    ): Response {
        try {
            $webhookEndpoint = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $endpointDto = $factory->createAppDto($webhookEndpoint);

        $mainDto = new ViewWebhookEndpoint();
        $mainDto->setWebhookEndpoint($endpointDto);

        $json = $serializer->serialize($mainDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/developer/webhook/event', name: 'app_app_developer_webhook_event_listevents', methods: ['GET'])]
    public function listEvents(
        Request $request,
        WebhookEventRepositoryInterface $repository,
        SerializerInterface $serializer,
        WebhookEventDataMapper $factory,
    ): Response {
        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $resultsPerPage = (int) $request->get('per_page', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $resultSet = $repository->getList(
            filters: [],
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$factory, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/developer/webhook/event/{id}/view', name: 'app_app_developer_webhook_viewevent', methods: ['GET'])]
    public function viewEvent(
        Request $request,
        WebhookEventRepositoryInterface $repository,
        SerializerInterface $serializer,
        WebhookEventDataMapper $factory,
        WebhookEventResponseDataMapper $responseDataMapper,
    ): Response {
        try {
            /** @var WebhookEvent $event */
            $event = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $eventDto = $factory->createAppDto($event);
        $responseDtos = array_map([$responseDataMapper, 'createAppDto'], $event->getResponses()->toArray());
        $viewDto = new ViewWebhookEvent();
        $viewDto->setEvent($eventDto);
        $viewDto->setResponses($responseDtos);

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
