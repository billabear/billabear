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

use App\Dto\Request\App\EmailTemplate\CreateEmailTemplate;
use App\Factory\EmailTemplateFactory;
use App\Repository\EmailTemplateRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailTemplateController
{
    #[Route('/app/settings/email-template', name: 'app_app_settings_emailtemplate_create', methods: ['POST'])]
    public function create(
        Request $request,
        EmailTemplateRepositoryInterface $repository,
        EmailTemplateFactory $factory,
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
}
