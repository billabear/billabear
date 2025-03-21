<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Developer;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Developer\WebhookEndpointDataMapper;
use BillaBear\DataMappers\Developer\WebhookEventDataMapper;
use BillaBear\DataMappers\Developer\WebhookEventResponseDataMapper;
use BillaBear\Dto\Request\App\Developer\Webhook\CreateWebhookEndpoint;
use BillaBear\Dto\Response\App\Developer\Webhook\ViewWebhookEndpoint;
use BillaBear\Dto\Response\App\Developer\Webhook\ViewWebhookEvent;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Entity\WebhookEvent;
use BillaBear\Repository\WebhookEndpointRepositoryInterface;
use BillaBear\Repository\WebhookEventRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WebhookController
{
    use ValidationErrorResponseTrait;
    use CrudListTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/developer/webhook', name: 'app_app_developer_webhook_createwebhook', methods: ['POST'])]
    public function createWebhook(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        WebhookEndpointDataMapper $webhookEndpointDataMapper,
        WebhookEndpointRepositoryInterface $repository,
    ): Response {
        $this->getLogger()->info('Received app request to create webhook');
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
        $this->getLogger()->info('Received app request to list webhooks');
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
        $this->getLogger()->info('Received app request to view webhook', ['webhook_id' => $request->get('id')]);
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
        $this->getLogger()->info('Received app request to list webhook event');

        return $this->crudList($request, $repository, $serializer, $factory);
    }

    #[Route('/app/developer/webhook/event/{id}/view', name: 'app_app_developer_webhook_viewevent', methods: ['GET'])]
    public function viewEvent(
        Request $request,
        WebhookEventRepositoryInterface $repository,
        SerializerInterface $serializer,
        WebhookEventDataMapper $factory,
        WebhookEventResponseDataMapper $responseDataMapper,
    ): Response {
        $this->getLogger()->info('Received app request to view webhook event', ['webhook_event_id' => $request->get('id')]);
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

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
