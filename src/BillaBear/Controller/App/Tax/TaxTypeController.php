<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Tax;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\TaxTypeDataMapper;
use BillaBear\Dto\Request\App\TaxType\CreateTaxType;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
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
    use LoggerAwareTrait;

    #[Route('/app/tax/type', name: 'app_app_create_tax_type', methods: ['POST'])]
    public function createTaxType(
        Request $request,
        TaxTypeDataMapper $taxTypeDataMapper,
        TaxTypeRepositoryInterface $taxTypeRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to create tax type');

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
        $this->getLogger()->info('Received request to list tax types');

        return $this->crudList($request, $taxTypeRepository, $serializer, $taxTypeDataMapper, 'name');
    }

    #[Route('/app/tax/type/{id}/default', name: 'app_app_create_tax_type_default', methods: ['POST'])]
    public function defaultTaxType(
        Request $request,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to read tax type', ['tax_type_id' => $request->get('id')]);

        try {
            $entity = $taxTypeRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $entity->setDefault(true);
        $taxTypeRepository->removeDefault();
        $taxTypeRepository->save($entity);

        return $this->crudList($request, $taxTypeRepository, $serializer, $taxTypeDataMapper, 'name');
    }

    #[Route('/app/tax/type/{id}/update', name: 'billabear_tax_type_update_read', methods: ['GET'])]
    public function readUpdate(
        Request $request,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to write tax type read', ['tax_type_id' => $request->get('id')]);

        try {
            $entity = $taxTypeRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
        $dto = $taxTypeDataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/tax/type/{id}/update', name: 'billabear_tax_type_update_write', methods: ['POST'])]
    public function writeUpdate(
        Request $request,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ) {
        $this->getLogger()->info('Received request to update tax type write');

        try {
            $entity = $taxTypeRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
        $createDto = $serializer->deserialize($request->getContent(), CreateTaxType::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }
        $entity = $taxTypeDataMapper->createEntity($createDto, $entity);
        $taxTypeRepository->save($entity);
        $dto = $taxTypeDataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }
}
