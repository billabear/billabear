<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Tax;

use BillaBear\Controller\App\CrudListTrait;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\CountryDataMapper;
use BillaBear\DataMappers\EconomicAreaDataMapper;
use BillaBear\DataMappers\EconomicAreaMembershipDatamapper;
use BillaBear\Dto\Request\App\EconomicArea\CreateEconomicArea;
use BillaBear\Dto\Request\App\EconomicArea\CreateMembership;
use BillaBear\Dto\Request\App\EconomicArea\UpdateMembership;
use BillaBear\Dto\Response\App\EconomicArea\ViewCreate;
use BillaBear\Repository\CountryRepositoryInterface;
use BillaBear\Repository\EconomicAreaMembershipRepositoryInterface;
use BillaBear\Repository\EconomicAreaRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EconomicAreaController
{
    use ValidationErrorResponseTrait;
    use CrudListTrait;

    #[Route('/app/economic-areas', name: 'billabear_list_areas', methods: ['GET'])]
    public function listAreas(
        Request $request,
        EconomicAreaRepositoryInterface $repository,
        EconomicAreaDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        return $this->crudList($request, $repository, $serializer, $dataMapper);
    }

    #[Route('/app/economic-area', methods: ['POST'])]
    public function addAction(
        Request $request,
        EconomicAreaRepositoryInterface $repository,
        EconomicAreaDataMapper $dataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        /** @var CreateEconomicArea $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateEconomicArea::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }
        $entity = $dataMapper->createEntity($dto);
        $repository->save($entity);
        $appDto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }

    #[Route('/app/economic-area/{id}/view', methods: ['GET'])]
    public function viewArea(
        Request $request,
        EconomicAreaRepositoryInterface $repository,
        EconomicAreaDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        try {
            $area = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($area);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, status: Response::HTTP_OK, json: true);
    }

    #[Route('/app/economic-area/member', methods: ['GET'])]
    public function readAddAction(
        CountryRepositoryInterface $countryRepository,
        CountryDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $countries = $countryRepository->getAll();
        $countryDtos = array_map([$dataMapper, 'createAppDto'], $countries);

        $dto = new ViewCreate();
        $dto->setCountries($countryDtos);

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }

    #[Route('/app/economic-area/member', methods: ['POST'])]
    public function addMemberAction(
        Request $request,
        EconomicAreaMembershipRepositoryInterface $repository,
        EconomicAreaMembershipDatamapper $dataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        /** @var CreateMembership $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateMembership::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }
        $entity = $dataMapper->createEntity($dto);
        $repository->save($entity);
        $appDto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }

    #[Route('/app/economic-area/member/{id}/update', methods: ['POST'])]
    public function updateMemberAction(
        Request $request,
        EconomicAreaMembershipRepositoryInterface $repository,
        EconomicAreaMembershipDatamapper $dataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        try {
            $member = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        /** @var UpdateMembership $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdateMembership::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }
        $entity = $dataMapper->updateEntity($dto, $member);
        $repository->save($entity);
        $appDto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }

    #[Route('/app/economic-area/member/{id}/view', methods: ['GET'])]
    public function viewMemberAction(
        Request $request,
        EconomicAreaMembershipRepositoryInterface $repository,
        EconomicAreaMembershipDatamapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        try {
            $member = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        $appDto = $dataMapper->createAppDto($member);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_OK, json: true);
    }

    #[Route('/app/economic-area/member/{id}/delete', methods: ['POST'])]
    public function deleteMemberAction(
        Request $request,
        EconomicAreaMembershipRepositoryInterface $repository,
    ): Response {
        try {
            $member = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        $repository->remove($member);

        return new JsonResponse([], status: Response::HTTP_OK);
    }
}
