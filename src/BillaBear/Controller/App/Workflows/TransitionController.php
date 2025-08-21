<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Workflows;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Workflows\PlaceDataMapper;
use BillaBear\Dto\Request\App\Workflows\CreateTransition;
use BillaBear\Repository\WorkflowTransitionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_DEVELOPER')]
class TransitionController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/workflow/create-transition', methods: ['POST'])]
    public function createTransition(
        Request $request,
        PlaceDataMapper $transitionHandlerDataMapper,
        WorkflowTransitionRepositoryInterface $workflowTransitionRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to create transition');

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
        $this->getLogger()->info('Received request to disable transition', ['transition_id' => $request->get('id')]);

        try {
            $entity = $workflowTransitionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $entity->setEnabled(false);
        $workflowTransitionRepository->save($entity);

        return new JsonResponse([], status: JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/workflow/transition/{id}/delete', methods: ['POST'])]
    public function deleteTransition(
        Request $request,
        WorkflowTransitionRepositoryInterface $workflowTransitionRepository,
    ): Response {
        $this->getLogger()->info('Received request to delete transition', ['transition_id' => $request->get('id')]);
        try {
            $entity = $workflowTransitionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $workflowTransitionRepository->delete($entity);

        return new JsonResponse([], status: JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/workflow/transition/{id}/enable', methods: ['POST'])]
    public function enableTransition(
        Request $request,
        WorkflowTransitionRepositoryInterface $workflowTransitionRepository,
    ): Response {
        $this->getLogger()->info('Received request to enable transition', ['transition_id' => $request->get('id')]);

        try {
            $entity = $workflowTransitionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $entity->setEnabled(true);
        $workflowTransitionRepository->save($entity);

        return new JsonResponse([], status: JsonResponse::HTTP_ACCEPTED);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
