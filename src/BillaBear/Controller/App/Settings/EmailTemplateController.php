<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\DataMappers\Settings\EmailTemplateDataMapper;
use BillaBear\Dto\Request\App\EmailTemplate\CreateEmailTemplate;
use BillaBear\Dto\Request\App\EmailTemplate\UpdateEmailTemplate;
use BillaBear\Dto\Response\App\EmailTemplate\EmailTemplateView;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Entity\EmailTemplate;
use BillaBear\Entity\User;
use BillaBear\Filters\EmailTemplateList;
use BillaBear\Notification\Email\EmailTester;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\EmailTemplateRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class EmailTemplateController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/settings/email-template/create', name: 'app_app_settings_emailtemplate_create_read', methods: ['GET'])]
    public function createRead(
        BrandSettingsRepositoryInterface $brandSettingRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view create email template');

        $brands = $brandSettingRepository->getAll();
        $brandData = [];

        foreach ($brands as $brand) {
            $brandData[$brand->getCode()] = $brand->getBrandName();
        }

        $dto = new \BillaBear\Dto\Response\App\EmailTemplate\CreateEmailTemplate();
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
        $this->getLogger()->info('Received request to create email template');

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
        $this->getLogger()->info('Received request to list email template');

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
        $this->getLogger()->info('Received request to view email template', ['email_template_id' => $request->get('id')]);
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
        $this->getLogger()->info('Received request to update email template', ['email_template_id' => $request->get('id')]);
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

    #[Route('/app/settings/email-template/{id}/test', name: 'app_app_settings_emailtemplate_test', methods: ['POST'])]
    public function sendTestEmail(
        #[CurrentUser]
        User $user,
        Request $request,
        EmailTemplateRepositoryInterface $repository,
        EmailTester $emailTester,
    ) {
        $this->getLogger()->info('Received request to update email template', ['email_template_id' => $request->get('id')]);
        try {
            $template = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $emailTester->sendTest($user, $template);

        return new JsonResponse(['success' => true]);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
