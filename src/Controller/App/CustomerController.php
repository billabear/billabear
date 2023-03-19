<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Api\Filters\CustomerList;
use App\Customer\CustomerFactory;
use App\Customer\ExternalRegisterInterface;
use App\Dto\CreateCustomerDto;
use App\Dto\Response\App\CustomerView;
use App\Dto\Response\App\ListResponse;
use App\Entity\Customer;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController
{
    // Currently a copy of the API but separated so in the future the two aren't coupled.
    #[Route('/app/customer', name: 'site_customer_list', methods: ['GET'])]
    public function listCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
    ): Response {
        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $resultsPerPage = (int) $request->get('per_page', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $filterBuilder = new CustomerList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $customerRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$customerFactory, 'createAppDtoFromCustomer'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/customer', name: 'app_customer_create', methods: ['POST'])]
    public function createCustomer(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerFactory $customerFactory,
        ExternalRegisterInterface $externalRegister,
        CustomerRepositoryInterface $customerRepository
    ): Response {
        $dto = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $customer = $customerFactory->createCustomer($dto);

        if ($customerRepository->hasCustomerByEmail($customer->getBillingEmail())) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_CONFLICT);
        }

        if (!$customer->hasExternalsCustomerReference()) {
            $externalRegister->register($customer);
        }
        $customerRepository->save($customer);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_CREATED);
    }

    #[Route('/app/customer/{id}', name: 'app_customer_view', methods: ['GET'])]
    public function viewCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        CustomerFactory $customerFactory,
    ): Response {
        try {
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_NOT_FOUND);
        }

        $customerDto = $customerFactory->createAppDtoFromCustomer($customer);
        $dto = new CustomerView();
        $dto->setCustomer($customerDto);
        $output = $serializer->serialize($dto, 'json');

        return new JsonResponse($output, json: true);
    }

    #[Route('/app/customer/{id}', name: 'app_customer_update', methods: ['POST'])]
    public function updateCustomer(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerFactory $customerFactory,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var CreateCustomerDto $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $newCustomer = $customerFactory->createCustomer($dto, $customer);

        $customerRepository->save($newCustomer);
        $dto = $customerFactory->createAppDtoFromCustomer($newCustomer);
        $jsonResponse = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_ACCEPTED, json: true);
    }
}
