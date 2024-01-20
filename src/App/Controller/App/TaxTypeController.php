<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\TaxTypeDataMapper;
use App\Dto\Request\App\TaxType\CreateTaxType;
use App\Repository\TaxTypeRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaxTypeController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/tax/type', name: 'app_app_create_tax_type', methods: ['POST'])]
    public function createTaxType(
        Request $request,
        TaxTypeDataMapper $taxTypeDataMapper,
        TaxTypeRepositoryInterface $taxTypeRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $createDto = $serializer->deserialize($request->getContent(), CreateTaxType::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $taxTypeDataMapper->createEntity($createDto);
        $taxTypeRepository->save($entity);
        $dto = $taxTypeDataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
