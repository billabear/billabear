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
use App\DataMappers\CountryDataMapper;
use App\Dto\Request\App\Country\CreateCountry;
use App\Repository\CountryRepositoryInterface;
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

        return new JsonResponse($json, json: true);
    }
}
