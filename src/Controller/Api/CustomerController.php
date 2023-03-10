<?php

namespace App\Controller\Api;

use App\Customer\CustomerFactory;
use App\Customer\ExternalRegisterInterface;
use App\Dto\CreateCustomerDto;
use App\Repository\CustomerRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController
{
    #[Route('/api/1.0/customer', name: 'api_customer_create',  methods: ['PUT'])]
    public function createCustomer(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerFactory $customerFactory,
        ExternalRegisterInterface $externalRegister,
        CustomerRepositoryInterface $customerRepository
    ): Response {

        $dto = $serializer->deserialize($request->getContent(),CreateCustomerDto::class,'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $customer = $customerFactory->createCustomer($dto);
        $externalRegister->register($customer);
        $customerRepository->save($customer);

        return new JsonResponse(['success' => true], JsonResponse::HTTP_CREATED);
    }
}