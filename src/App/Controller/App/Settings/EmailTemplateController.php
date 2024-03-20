<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Settings;

use App\DataMappers\Settings\EmailTemplateDataMapper;
use App\Dto\Request\App\EmailTemplate\CreateEmailTemplate;
use App\Dto\Request\App\EmailTemplate\UpdateEmailTemplate;
use App\Dto\Response\App\EmailTemplate\EmailTemplateView;
use App\Dto\Response\App\ListResponse;
use App\Entity\EmailTemplate;
use App\Filters\EmailTemplateList;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\EmailTemplateRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class EmailTemplateController
{
    #[Route('/app/settings/email-template/create', name: 'app_app_settings_emailtemplate_create_read', methods: ['GET'])]
    public function createRead(
        BrandSettingsRepositoryInterface $brandSettingRepository,
        SerializerInterface $serializer,
    ): Response {
        $brands = $brandSettingRepository->getAll();
        $brandData = [];

        foreach ($brands as $brand) {
            $brandData[$brand->getCode()] = $brand->getBrandName();
        }

        $dto = new \App\Dto\Response\App\EmailTemplate\CreateEmailTemplate();
        $dto->setTemplateNames(EmailTemplate::TEMPLATE_NAMES);
        $dto->setBrands($brandData);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/email-template', name: 'app_app_settings_emailtemplate_create', methods: ['POST'])]
    public function create(
        Request $request,
        EmailTemplateRepositoryInterface $repository,
        EmailTemplateDataMapper $factory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        /** @var CreateEmailTemplate $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateEmailTemplate::class, 'json');
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

        $emailTemplate = $factory->createEntity($dto);
        $repository->save($emailTemplate);

        $outputDto = $factory->createAppDto($emailTemplate);

        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/email-template', name: 'app_app_settings_emailtemplate_list', methods: ['GET'])]
    public function listTemplates(
        Request $request,
        EmailTemplateRepositoryInterface $repository,
        EmailTemplateDataMapper $factory,
        SerializerInterface $serializer,
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

        $filterBuilder = new EmailTemplateList();
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

    #[Route('/app/settings/email-template/{id}', name: 'app_app_settings_emailtemplate_read', methods: ['GET'])]
    public function read(
        Request $request,
        EmailTemplateRepositoryInterface $repository,
        EmailTemplateDataMapper $factory,
        SerializerInterface $serializer,
    ): Response {
        try {
            $template = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $dto = new EmailTemplateView();
        $dto->setEmailTemplate($factory->createFullAppDto($template));

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/email-template/{id}', name: 'app_app_settings_emailtemplate_update', methods: ['POST'])]
    public function update(
        Request $request,
        EmailTemplateRepositoryInterface $repository,
        EmailTemplateDataMapper $factory,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        try {
            $template = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var UpdateEmailTemplate $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdateEmailTemplate::class, 'json');
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

        $template = $factory->updateEntity($dto, $template);
        $repository->save($template);
        $outputDto = $factory->createAppDto($template);

        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
