<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Settings;

use App\Api\Filters\CustomerList;
use App\Dto\Request\App\Settings\User\UserUpdate;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Settings\User\UserView;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ADMIN')]
class UserController
{
    #[Route('/app/settings/user', name: 'site_settings_users_list', methods: ['GET'])]
    public function readUserList(
        Request $request,
        UserRepositoryInterface $repository,
        SerializerInterface $serializer,
        UserFactory $factory,
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

        $filterBuilder = new CustomerList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $repository->getList(
            filters: $filters,
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

    #[Route('/app/settings/user/{id}', name: 'site_settings_users_view', methods: ['GET'])]
    public function viewUser(
        Request $request,
        UserRepositoryInterface $userRepository,
        SerializerInterface $serializer,
        UserFactory $userFactory,
    ): Response {
        try {
            $user = $userRepository->getById($request->get('id'), true);
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $userFactory->createAppDto($user);
        $userView = new UserView();
        $userView->setUser($dto);
        $userView->setRoles(User::ROLES);
        $json = $serializer->serialize($userView, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/user/{id}', name: 'site_settings_users_update', methods: ['POST'])]
    public function updateUser(
        Request $request,
        UserRepositoryInterface $userRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserFactory $userFactory,
    ): Response {
        try {
            $user = $userRepository->getById($request->get('id'), true);
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $input = $serializer->deserialize($request->getContent(), UserUpdate::class, 'json');
        $errors = $validator->validate($input);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userFactory->updateEntity($user, $input);
        $userRepository->save($user);
        $dto = $userFactory->createAppDto($user);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
