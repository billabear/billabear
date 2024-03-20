<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\CountryDataMapper;
use App\DataMappers\CountryTaxRuleDataMapper;
use App\DataMappers\TaxTypeDataMapper;
use App\Dto\Request\App\Country\CreateCountry;
use App\Dto\Request\App\Country\CreateCountryTaxRule;
use App\Dto\Request\App\Country\UpdateCountry;
use App\Dto\Request\App\Country\UpdateCountryTaxRule;
use App\Dto\Response\App\Country\CountryView;
use App\Repository\CountryRepositoryInterface;
use App\Repository\CountryTaxRuleRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
use App\Tax\CountryTaxRuleTerminator;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CountryController
{
    use ValidationErrorResponseTrait;
    use CrudListTrait;

    #[Route('/app/countries', name: 'app_country_list', methods: ['GET'])]
    public function listPayment(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        SerializerInterface $serializer,
        CountryDataMapper $paymentFactory,
    ): Response {
        return $this->crudList($request, $countryRepository, $serializer, $paymentFactory);
    }

    #[Route('/app/country', methods: ['POST'])]
    public function addAction(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        CountryDataMapper $dataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        /** @var CreateCountry $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateCountry::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }
        $entity = $dataMapper->createEntity($dto);
        $countryRepository->save($entity);
        $appDto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }

    #[Route('/app/country/{id}/view', methods: ['GET'])]
    public function readCountry(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
        CountryTaxRuleDataMapper $countryTaxRuleDataMapper,
        CountryDataMapper $dataMapper,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
        SerializerInterface $serializer,
    ): Response {
        try {
            $entity = $countryRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $countryDto = $dataMapper->createAppDto($entity);

        $countryTaxRules = $countryTaxRuleRepository->getForCountry($entity);
        $countryTaxRuleDtos = array_map([$countryTaxRuleDataMapper, 'createAppDto'], $countryTaxRules);

        $taxTypes = $taxTypeRepository->getAll();
        $taxTypesDtos = array_map([$taxTypeDataMapper, 'createAppDto'], $taxTypes);

        $view = new CountryView();
        $view->setCountry($countryDto);
        $view->setCountryTaxRules($countryTaxRuleDtos);
        $view->setTaxTypes($taxTypesDtos);
        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/country/{id}/edit', methods: ['POST'])]
    public function editCountry(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        CountryDataMapper $dataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        try {
            $entity = $countryRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var UpdateCountry $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdateCountry::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }
        $entity = $dataMapper->createEntity($dto, $entity);
        $countryRepository->save($entity);
        $appDto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/country/{id}/tax-rule', methods: ['POST'])]
    public function createTaxRule(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        CountryTaxRuleDataMapper $countryTaxRuleDataMapper,
        CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
        CountryTaxRuleTerminator $countryTaxRuleTerminator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        try {
            $country = $countryRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }
        $createDto = $serializer->deserialize($request->getContent(), CreateCountryTaxRule::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $countryTaxRuleDataMapper->createEntity($createDto, $country);
        $countryTaxRuleTerminator->terminateOpenTaxRule($entity);
        $countryTaxRuleRepository->save($entity);
        $appDto = $countryTaxRuleDataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }

    #[Route('/app/country/{id}/tax-rule/{taxRuleId}/edit', methods: ['POST'])]
    public function editTaxRule(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        CountryTaxRuleDataMapper $countryTaxRuleDataMapper,
        CountryTaxRuleRepositoryInterface $countryTaxRuleRepository,
        CountryTaxRuleTerminator $countryTaxRuleTerminator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        try {
            $country = $countryRepository->findById($request->get('id'));
            $entity = $countryTaxRuleRepository->findById($request->get('taxRuleId'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $createDto = $serializer->deserialize($request->getContent(), UpdateCountryTaxRule::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $countryTaxRuleDataMapper->createEntity($createDto, $country, $entity);
        $countryTaxRuleTerminator->terminateOpenTaxRule($entity);
        $countryTaxRuleRepository->save($entity);
        $appDto = $countryTaxRuleDataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }
}
