<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Integrations;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Integrations\SlackNotificationDataMapper;
use BillaBear\DataMappers\Integrations\SlackWebhookDataMapper;
use BillaBear\Dto\Request\App\Integrations\Slack\CreateSlackNotification;
use BillaBear\Dto\Request\App\Integrations\Slack\CreateSlackWebhook;
use BillaBear\Entity\SlackWebhook;
use BillaBear\Repository\SlackNotificationRepositoryInterface;
use BillaBear\Repository\SlackWebhookRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
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
    use CrudListTrait;

    #[Route('/app/integrations/slack/notification/create', name: 'billabear_app_integrations_slack_createnotification', methods: ['POST'])]
    public function createNotification(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SlackNotificationRepositoryInterface $notificationRepository,
        SlackNotificationDataMapper $notificationDataMapper,
    ): Response {
        /** @var CreateSlackNotification $createDto */
        $createDto = $serializer->deserialize($request->getContent(), CreateSlackNotification::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $notificationDataMapper->createEntity($createDto);
        $notificationRepository->save($entity);
        $dto = $notificationDataMapper->createAppDto($entity);
        $json = $serializer->serialize($entity, 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, json: true);
    }

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
        $dto = $slackWebhookDataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, json: true);
    }

    #[Route('/app/integrations/slack/webhook', name: 'billabear_app_integrations_slack_showlist', methods: ['GET'])]
    public function showList(
        Request $request,
        SlackWebhookRepositoryInterface $repository,
        SerializerInterface $serializer,
        SlackWebhookDataMapper $factory,
    ): Response {
        return $this->crudList($request, $repository, $serializer, $factory);
    }

    #[Route('/app/integrations/slack/webhook/{id}/disable', name: 'billabear_app_integrations_slack_disablewebhook', methods: ['POST'])]
    public function disableWebhook(
        Request $request,
        SlackWebhookRepositoryInterface $repository,
        SlackWebhookDataMapper $mapper,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            /** @var SlackWebhook $slackWebhook */
            $slackWebhook = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $slackWebhook->setEnabled(false);
        $repository->save($slackWebhook);
        $dto = $mapper->createAppDto($slackWebhook);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/app/integrations/slack/webhook/{id}/enable', name: 'billabear_app_integrations_slack_enablewebhook', methods: ['POST'])]
    public function enableWebhook(
        Request $request,
        SlackWebhookRepositoryInterface $repository,
        SlackWebhookDataMapper $mapper,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            /** @var SlackWebhook $slackWebhook */
            $slackWebhook = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $slackWebhook->setEnabled(true);
        $repository->save($slackWebhook);
        $dto = $mapper->createAppDto($slackWebhook);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
