<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\DataMappers\Settings\BrandSettingsDataMapper;
use BillaBear\Dto\Request\App\BrandSettings\CreateBrandSettings;
use BillaBear\Dto\Request\App\BrandSettings\EditBrandSettings;
use BillaBear\Dto\Response\App\BrandSettings\BrandSettingsView;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class BrandSettingsController
{
    use LoggerAwareTrait;

    #[Route('/app/settings/brand', name: 'app_settings_brand_list', methods: ['GET'])]
    public function listBrandSettings(
        Request $request,
        BrandSettingsRepositoryInterface $brandSettingRepository,
        BrandSettingsDataMapper $brandFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to list brand settings');

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
        BrandSettingsDataMapper $brandSettingsFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view brand settings', ['brand_settings_id' => $request->get('id')]);

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
        BrandSettingsDataMapper $brandSettingsFactory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        $this->getLogger()->info('Received request to edit brand settings', ['brand_settings_id' => $request->get('id')]);

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
        BrandSettingsDataMapper $brandSettingsFactory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        $this->getLogger()->info('Received request to create brand settings');

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
