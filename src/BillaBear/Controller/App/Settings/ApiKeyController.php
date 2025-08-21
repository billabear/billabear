<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\Background\Generic\GenericTasks;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Settings\ApiKeyDataMapper;
use BillaBear\Dto\Request\App\Settings\CreateApiKey;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Entity\ApiKey;
use BillaBear\Repository\ApiKeyRepositoryInterface;
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
class ApiKeyController
{
    use ValidationErrorResponseTrait;

    public function __construct(private readonly GenericTasks $genericTasks, private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/settings/api-key', name: 'app_app_settings_apikey_showlist', methods: ['GET'])]
    public function showList(
        ApiKeyRepositoryInterface $apiKeyRepository,
        ApiKeyDataMapper $apiKeyFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to show list of api keys');

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
        ApiKeyDataMapper $apiKeyFactory,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to create api key');

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

    #[Route('/app/settings/api-key/{id}/disable', name: 'app_app_settings_apikey_disableapikey', methods: ['POST'])]
    public function disableApiKey(
        Request $request,
        ApiKeyRepositoryInterface $apiKeyRepository,
    ): Response {
        $this->getLogger()->info('Received request to disable api key', ['api_key_id' => $request->get('id')]);

        try {
            /** @var ApiKey $apiKey */
            $apiKey = $apiKeyRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $apiKey->setActive(false);
        $apiKeyRepository->save($apiKey);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
