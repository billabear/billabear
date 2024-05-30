<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Tax;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Tax\StateDataMapper;
use BillaBear\Dto\Request\App\Country\CreateState;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StateController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/country/{id}/state', methods: ['POST'])]
    public function editTaxRule(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        StateRepositoryInterface $stateRepository,
        StateDataMapper $stateDataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        try {
            $country = $countryRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $createDto = $serializer->deserialize($request->getContent(), CreateState::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $stateDataMapper->createEntity($createDto);
        $stateRepository->save($entity);
        $appDto = $stateDataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }
}
