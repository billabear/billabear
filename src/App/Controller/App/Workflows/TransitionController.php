<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Workflows;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\Workflows\PlaceDataMapper;
use App\Dto\Request\App\Workflows\CreateTransition;
use App\Repository\WorkflowTransitionRepositoryInterface;
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
}