<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Usage\MetricDataMapper;
use BillaBear\Dto\Request\App\Usage\CreateMetric;
use BillaBear\Dto\Request\App\Usage\UpdateMetric;
use BillaBear\Repository\Usage\MetricRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MetricController
{
    use LoggerAwareTrait;
    use ValidationErrorResponseTrait;
    use CrudListTrait;

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/metric', name: 'billabear_metrics_create_write', methods: ['POST'])]
    public function createMetric(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MetricDataMapper $dataMapper,
        MetricRepositoryInterface $metricRepository,
    ) {
        $this->getLogger()->info('Received a request to create a metric via the app');

        $createDto = $serializer->deserialize($request->getContent(), CreateMetric::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $dataMapper->createEntity($createDto);
        $metricRepository->save($entity);
        $appDto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return JsonResponse::fromJsonString($json);
    }

    #[Route('/app/metric/list', name: 'billabear_metrics_list', methods: ['GET'])]
    public function listMetrics(
        Request $request,
        MetricRepositoryInterface $metricRepository,
        SerializerInterface $serializer,
        MetricDataMapper $dataMapper,
    ) {
        $this->getLogger()->info('Received request to list metrics via the app');

        return $this->crudList($request, $metricRepository, $serializer, $dataMapper);
    }

    #[Route('/app/metric/{id}/read', name: 'billabear_metrics_read', methods: ['GET'])]
    public function readMetric(
        Request $request,
        MetricRepositoryInterface $metricRepository,
        SerializerInterface $serializer,
        MetricDataMapper $dataMapper,
    ) {
        $this->getLogger()->info('Received request to read a metric via the app');

        try {
            $entity = $metricRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, Response::HTTP_OK, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/metric/{id}/update', name: 'billabear_metrics_update', methods: ['POST'])]
    public function updateMetric(
        Request $request,
        MetricRepositoryInterface $metricRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MetricDataMapper $dataMapper,
    ) {
        $this->getLogger()->info('Received request to update a metric via the app');

        try {
            $entity = $metricRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
        $updateDto = $serializer->deserialize($request->getContent(), UpdateMetric::class, 'json');
        $errors = $validator->validate($updateDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $dataMapper->createEntity($updateDto, $entity);
        $metricRepository->save($entity);
        $appDto = $dataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return JsonResponse::fromJsonString($json);
    }
}
