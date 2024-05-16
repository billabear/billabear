<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Integrations\SlackWebhookDataMapper;
use BillaBear\Dto\Request\App\Integrations\Slack\CreateSlackWebhook;
use BillaBear\Repository\SlackWebhookRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_DEVELOPER')]
class SlackController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/integrations/slack/webhook/create', name: 'billabear_app_integrations_slack_createwebhook', methods: ['POST'])]
    public function createWebhook(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SlackWebhookRepositoryInterface $slackWebhookRepository,
        SlackWebhookDataMapper $slackWebhookDataMapper,
    ): Response {
        /** @var CreateSlackWebhook $createDto */
        $createDto = $serializer->deserialize($request->getContent(), CreateSlackWebhook::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $slackWebhookDataMapper->buildEntity($createDto);
        $slackWebhookRepository->save($entity);
        $json = $serializer->serialize($entity, 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, json: true);
    }
}
