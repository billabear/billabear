<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Tax;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Tax\StateDataMapper;
use BillaBear\DataMappers\Tax\StateTaxRuleDataMapper;
use BillaBear\Dto\Request\App\Country\CreateState;
use BillaBear\Dto\Request\App\Country\CreateStateTaxRule;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use BillaBear\Repository\StateTaxRuleRepositoryInterface;
use BillaBear\Tax\StateTaxRuleTerminator;
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

    #[Route('/app/country/{id}/state/{stateId}/tax-rule', methods: ['POST'])]
    public function createStateTax(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        StateRepositoryInterface $stateRepository,
        StateTaxRuleDataMapper $stateTaxRuleDataMapper,
        StateTaxRuleRepositoryInterface $stateTaxRuleRepository,
        StateTaxRuleTerminator $stateTaxRuleTerminator,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        try {
            $country = $countryRepository->findById($request->get('id'));
            $state = $stateRepository->findById($request->get('stateId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $createDto = $serializer->deserialize($request->getContent(), CreateStateTaxRule::class, 'json');
        $errors = $validator->validate($createDto);
        $response = $this->handleErrors($errors);
        if ($response instanceof Response) {
            return $response;
        }

        $entity = $stateTaxRuleDataMapper->createEntity($createDto);
        $stateTaxRuleRepository->save($entity);
        $stateTaxRuleTerminator->terminateOpenTaxRule($entity);
        $dto = $stateTaxRuleDataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/country/{id}/state', methods: ['POST'])]
    public function createState(
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
