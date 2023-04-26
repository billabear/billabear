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
use App\Factory\BrandSettingsFactory;
use App\Repository\BrandSettingRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BrandSettingsController
{
    #[Route('/app/settings/brand', name: 'app_settings_brand_list', methods: ['GET'])]
    public function listBrandSettings(
        Request $request,
        BrandSettingRepositoryInterface $brandSettingRepository,
        BrandSettingsFactory $brandFactory,
        SerializerInterface $serializer,
    ): Response {
        $brands = $brandSettingRepository->getAll();

        $dtos = array_map([$brandFactory, 'createAppDto'], $brands);

        $list = new ListResponse();
        $list->setData($dtos);
        $list->setHasMore(false);
        $list->setFirstKey(null);
        $list->setLastKey(null);

        $json = $serializer->serialize($list, 'json');

        return new JsonResponse($json, json: true);
    }
}
