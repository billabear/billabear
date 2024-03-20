<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
    use CrudListTrait;

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

    #[Route('/app/tax/type', name: 'app_app_list_tax_type', methods: ['GET'])]
    public function listTaxTypes(
        Request $request,
        TaxTypeDataMapper $taxTypeDataMapper,
        TaxTypeRepositoryInterface $taxTypeRepository,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $taxTypeRepository, $serializer, $taxTypeDataMapper, 'name');
    }
}
