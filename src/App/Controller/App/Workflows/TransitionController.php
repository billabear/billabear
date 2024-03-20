<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Workflows;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\Workflows\PlaceDataMapper;
use App\Dto\Request\App\Workflows\CreateTransition;
use App\Repository\WorkflowTransitionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransitionController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/workflow/create-transition', methods: ['POST'])]
    public function createTransition(
        Request $request,
        PlaceDataMapper $transitionHandlerDataMapper,
        WorkflowTransitionRepositoryInterface $workflowTransitionRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        /** @var CreateTransition $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateTransition::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $transitionHandlerDataMapper->createEntity($dto);
        $workflowTransitionRepository->save($entity);
        $outputDto = $transitionHandlerDataMapper->createAppDto($entity);
        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/workflow/transition/{id}/disable', methods: ['POST'])]
    public function disableTransition(
        Request $request,
        WorkflowTransitionRepositoryInterface $workflowTransitionRepository,
    ): Response {
        try {
            $entity = $workflowTransitionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $entity->setEnabled(false);
        $workflowTransitionRepository->save($entity);

        return new JsonResponse([], status: JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/workflow/transition/{id}/enable', methods: ['POST'])]
    public function enableTransition(
        Request $request,
        WorkflowTransitionRepositoryInterface $workflowTransitionRepository,
    ): Response {
        try {
            $entity = $workflowTransitionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $entity->setEnabled(true);
        $workflowTransitionRepository->save($entity);

        return new JsonResponse([], status: JsonResponse::HTTP_ACCEPTED);
    }
}
