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

use App\Dto\Response\App\ListResponse;
use App\Factory\Settings\ApiKeyFactory;
use App\Repository\ApiKeyRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiKeyController
{
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
}
