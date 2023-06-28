<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App\Settings;

use App\Dto\Request\App\BrandSettings\CreateBrandSettings;
use App\Dto\Request\App\BrandSettings\EditBrandSettings;
use App\Dto\Response\App\BrandSettings\BrandSettingsView;
use App\Dto\Response\App\ListResponse;
use App\Factory\Settings\BrandSettingsFactory;
use App\Repository\BrandSettingsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class BrandSettingsController
{
    #[Route('/app/settings/brand', name: 'app_settings_brand_list', methods: ['GET'])]
    public function listBrandSettings(
        Request $request,
        BrandSettingsRepositoryInterface $brandSettingRepository,
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

    #[Route('/app/settings/brand/{id}', name: 'app_settings_brand_view', methods: ['GET'])]
    public function viewBrandSettings(
        Request $request,
        BrandSettingsRepositoryInterface $brandSettingRepository,
        BrandSettingsFactory $brandSettingsFactory,
        SerializerInterface $serializer,
    ): Response {
        try {
            $brandSettings = $brandSettingRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $brandView = new BrandSettingsView();
        $brandView->setBrandSettings($brandSettingsFactory->createAppDto($brandSettings));

        $json = $serializer->serialize($brandView, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/brand/{id}', name: 'app_settings_brand_edit', methods: ['POST'])]
    public function editBrandSettings(
        Request $request,
        BrandSettingsRepositoryInterface $brandSettingRepository,
        BrandSettingsFactory $brandSettingsFactory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        try {
            $brandSettings = $brandSettingRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var EditBrandSettings $dto */
        $dto = $serializer->deserialize($request->getContent(), EditBrandSettings::class, 'json');
        $errors = $validator->validate($dto);

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

        $brandSettings = $brandSettingsFactory->createEntityFromEditDto($dto, $brandSettings);
        $brandSettingRepository->save($brandSettings);

        $brandView = new BrandSettingsView();
        $brandView->setBrandSettings($brandSettingsFactory->createAppDto($brandSettings));

        $json = $serializer->serialize($brandView, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/brand', name: 'app_settings_brand_create', methods: ['POST'])]
    public function createBrandSettings(
        Request $request,
        BrandSettingsRepositoryInterface $brandSettingRepository,
        BrandSettingsFactory $brandSettingsFactory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        /** @var CreateBrandSettings $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateBrandSettings::class, 'json');
        $errors = $validator->validate($dto);

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

        $brandSettings = $brandSettingsFactory->createEntityFromEditDto($dto);
        $brandSettingRepository->save($brandSettings);

        $brandView = new BrandSettingsView();
        $brandView->setBrandSettings($brandSettingsFactory->createAppDto($brandSettings));

        $json = $serializer->serialize($brandView, 'json');

        return new JsonResponse($json, json: true);
    }
}
