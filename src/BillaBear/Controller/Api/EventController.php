<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Usage\EventDataMapper;
use BillaBear\Dto\Request\Api\CreateEvent\CreateEvent;
use BillaBear\Repository\Usage\EventRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/events', name: 'api_v1_events', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EventDataMapper $eventDataMapper,
        EventRepositoryInterface $eventRepository,
    ): Response {
        $this->getLogger()->info('Received a request to create an event');
        /** @var CreateEvent $createDto */
        $createDto = $serializer->deserialize($request->getContent(), CreateEvent::class, 'json');
        $errors = $validator->validate($createDto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }

        $eventData = $eventDataMapper->createEntity($createDto);
        $eventRepository->save($eventData);

        return new Response(null, Response::HTTP_CREATED);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
