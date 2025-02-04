<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Usage;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Usage\UsageLimitDataMapper;
use BillaBear\Dto\Request\App\Usage\CreateUsageLimit;
use BillaBear\Entity\Customer;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\UsageLimitRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsageLimitController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{id}/usage-limit', name: 'app_customer_usage_limit', methods: ['POST'])]
    public function createUsageLimit(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        UsageLimitRepositoryInterface $usageLimitRepository,
        UsageLimitDataMapper $usageLimitDataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        $this->getLogger()->info('Received request to create a customer usage limit', ['customer_id' => $request->get('id')]);

        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $createDto = $serializer->deserialize($request->getContent(), CreateUsageLimit::class, 'json');
        $errors = $validator->validate($createDto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $usageLimitDataMapper->createEntityFromApp($customer, $createDto);
        $usageLimitRepository->save($entity);

        return new Response(null, Response::HTTP_CREATED);
    }

    #[IsGranted('ROLE_CUSTOMER_SUPPORT')]
    #[Route('/app/customer/{id}/usage-limit/{limitId}/delete', name: 'app_customer_usage_limit_delete', methods: ['POST'])]
    public function deleteUsageLimit(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        UsageLimitRepositoryInterface $usageLimitRepository,
    ): Response {
        $this->getLogger()->info('Received request to delete a customer usage limit', ['customer_id' => $request->get('id')]);

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
