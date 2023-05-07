<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Settings;

use App\Controller\ValidationErrorResponseTrait;
use App\Dto\Request\App\Settings\CreateApiKey;
use App\Dto\Response\App\ListResponse;
use App\Factory\Settings\ApiKeyFactory;
use App\Repository\ApiKeyRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiKeyController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/settings/api-key', name: 'app_app_settings_apikey_showlist', methods: ['GET'])]
    public function showList(
        ApiKeyRepositoryInterface $apiKeyRepository,
        ApiKeyFactory $apiKeyFactory,
        SerializerInterface $serializer,
    ): Response {
        $apiKeys = $apiKeyRepository->getAll();
        $dtos = array_map([$apiKeyFactory, 'createAppDto'], $apiKeys);
        $view = new ListResponse();
        $view->setData($dtos);
        $view->setHasMore(false);
        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/api-key', name: 'app_app_settings_apikey_createapi', methods: ['POST'])]
    public function createApi(
        Request $request,
        ApiKeyRepositoryInterface $apiKeyRepository,
        ApiKeyFactory $apiKeyFactory,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        /** @var CreateApiKey $createDto */
        $createDto = $serializer->deserialize($request->getContent(), CreateApiKey::class, 'json');
        $errors = $validator->validate($createDto);

        $errorResponse = $this->handleErrors($errors);
        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }
        $entity = $apiKeyFactory->createEntity($createDto);
        $apiKeyRepository->save($entity);
        $dto = $apiKeyFactory->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
