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
use BillaBear\DataMappers\TaxTypeDataMapper;
use BillaBear\Dto\Request\App\Country\CreateState;
use BillaBear\Dto\Request\App\Country\CreateStateTaxRule;
use BillaBear\Dto\Request\App\Country\UpdateState;
use BillaBear\Dto\Request\App\Country\UpdateStateTaxRule;
use BillaBear\Dto\Response\App\Tax\StateView;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\StateRepositoryInterface;
use BillaBear\Repository\StateTaxRuleRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use BillaBear\Tax\StateTaxRuleTerminator;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StateController
{
    use ValidationErrorResponseTrait;
    use LoggerAwareTrait;

    #[Route('/app/country/{id}/state/{stateId}/tax-rule/{taxRuleId}/edit', methods: ['POST'])]
    public function updateStateTaxRule(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        StateRepositoryInterface $stateRepository,
        StateTaxRuleDataMapper $stateTaxRuleDataMapper,
        StateTaxRuleRepositoryInterface $stateTaxRuleRepository,
        StateTaxRuleTerminator $stateTaxRuleTerminator,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $this->getLogger()->info('Received request to update state tax rule', [
            'country_id' => $request->get('id'),
            'state_id' => $request->get('stateId'),
        ]);

        try {
            $country = $countryRepository->findById($request->get('id'));
            $state = $stateRepository->findById($request->get('stateId'));
            $entity = $stateTaxRuleRepository->findById($request->get('taxRuleId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $createDto = $serializer->deserialize($request->getContent(), UpdateStateTaxRule::class, 'json');
        $errors = $validator->validate($createDto);
        $response = $this->handleErrors($errors);
        if ($response instanceof Response) {
            return $response;
        }

        $entity = $stateTaxRuleDataMapper->createEntity($createDto, $entity);
        $stateTaxRuleRepository->save($entity);
        $stateTaxRuleTerminator->terminateOpenTaxRule($entity);
        $dto = $stateTaxRuleDataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

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
        $this->getLogger()->info('Received request to create state tax rule', [
            'country_id' => $request->get('id'),
            'state_id' => $request->get('stateId'),
        ]);

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
        $this->getLogger()->info('Received request to create state', ['country_id' => $request->get('id')]);

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

    #[Route('/app/country/{id}/state/{stateId}/view', methods: ['GET'])]
    public function readState(
        Request $request,
        StateRepositoryInterface $stateRepository,
        StateDataMapper $stateDataMapper,
        StateTaxRuleRepositoryInterface $stateTaxRuleRepository,
        StateTaxRuleDataMapper $stateTaxRuleDataMapper,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
        SerializerInterface $serializer
    ): Response {
        $this->getLogger()->info('Received request to read state', [
            'country_id' => $request->get('id'),
            'state_id' => $request->get('stateId'),
        ]);
        try {
            $entity = $stateRepository->findById($request->get('stateId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $taxRules = $stateTaxRuleRepository->getForState($entity);
        $taxRulesDto = array_map([$stateTaxRuleDataMapper, 'createAppDto'], $taxRules);

        $taxTypes = $taxTypeRepository->getAll();
        $taxTypesDto = array_map([$taxTypeDataMapper, 'createAppDto'], $taxTypes);

        $dto = $stateDataMapper->createAppDto($entity);
        $view = new StateView();
        $view->setState($dto);
        $view->setTaxRules($taxRulesDto);
        $view->setTaxTypes($taxTypesDto);
        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/app/country/{id}/state/{stateId}/edit', methods: ['POST'])]
    public function editState(
        Request $request,
        StateRepositoryInterface $stateRepository,
        StateDataMapper $stateDataMapper,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): Response {
        $this->getLogger()->info('Received request to edit state', [
            'country_id' => $request->get('id'),
            'state_id' => $request->get('stateId'),
        ]);
        try {
            $entity = $stateRepository->findById($request->get('stateId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $createDto = $serializer->deserialize($request->getContent(), UpdateState::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $stateDataMapper->createEntity($createDto, $entity);
        $stateRepository->save($entity);
        $appDto = $stateDataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }
}
