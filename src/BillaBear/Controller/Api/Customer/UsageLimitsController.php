<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api\Customer;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Usage\UsageLimitDataMapper;
use BillaBear\Dto\Request\Api\Usage\CreateUsageLimit;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Entity\Customer;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\UsageLimitRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsageLimitsController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/customer/{id}/usage-limits', name: 'api_v1_customer_read_usage_limits', methods: ['GET'])]
    public function viewUsageLimits(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        UsageLimitRepositoryInterface $usageLimitRepository,
        UsageLimitDataMapper $usageLimitDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received an API request to list usage limits for a customer', ['customer_id' => $request->get('id')]);

        try {
            $customer = $customerRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $limits = $usageLimitRepository->getForCustomer($customer);
        $dtos = array_map([$usageLimitDataMapper, 'createApiDto'], $limits);

        $listResponse = new ListResponse();
        $listResponse->setData($dtos);
        $listResponse->setHasMore(false);
        $listResponse->setLastKey(null);

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, Response::HTTP_OK, json: true);
    }

    #[Route('/api/v1/customer/{id}/usage-limits', name: 'api_v1_customer_create_usage_limits', methods: ['POST'])]
    public function createUsageLimits(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        UsageLimitRepositoryInterface $usageLimitRepository,
        UsageLimitDataMapper $usageLimitDataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        $this->getLogger()->info('Received an API request to create a usage limit for a customer', ['customer_id' => $request->get('id')]);
        try {
            $customer = $customerRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $createDto = $serializer->deserialize($request->getContent(), CreateUsageLimit::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $usageLimitDataMapper->createEntityFromApi($customer, $createDto);
        $usageLimitRepository->save($entity);
        $outputDto = $usageLimitDataMapper->createApiDto($entity);
        $json = $serializer->serialize($outputDto, 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/customer/{id}/usage-limit/{limitId}', name: 'api_customer_usage_limit_delete', methods: ['DELETE'])]
    public function deleteUsageLimit(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        UsageLimitRepositoryInterface $usageLimitRepository,
    ): Response {
        $this->getLogger()->info('Received request via API to delete a customer usage limit', ['customer_id' => $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        try {
            $usageLimit = $usageLimitRepository->findById($request->get('limitId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $usageLimitRepository->delete($usageLimit);

        return new Response(null, Response::HTTP_OK);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
