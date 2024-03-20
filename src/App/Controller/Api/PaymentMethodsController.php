<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Api;

use App\DataMappers\PaymentMethodsDataMapper;
use App\Dto\Request\Api\PaymentDetails\FrontendTokenComplete;
use App\Dto\Response\Api\ListResponse;
use App\Dto\Response\Api\PaymentDetails\FrontendToken;
use App\Entity\Customer;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\PaymentMethod\DefaultPaymentManagerInterface;
use Parthenon\Billing\PaymentMethod\DeleterInterface;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentMethodsController
{
    #[Route('/api/v1/customer/{customerId}/payment-methods/frontend-payment-token', name: 'api_v1.0_payment_details_frontend_payment_token_start', methods: ['GET'])]
    public function startJsTokenAdd(
        Request $request,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $token = $addCardByTokenDriver->startTokenProcess($customer);
        $dto = new FrontendToken();
        $dto->setToken($token);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/api/v1/customer/{customerId}/payment-methods/frontend-payment-token', name: 'api_v1.0_payment_details_frontend_payment_token_complete', methods: ['POST'])]
    public function finishFrontendAdd(
        Request $request,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PaymentMethodsDataMapper $paymentDetailsFactory,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $serializer->deserialize($request->getContent(), FrontendTokenComplete::class, 'json');
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

        $paymentDetails = $addCardByTokenDriver->createPaymentDetailsFromToken($customer, $dto->getToken());

        $output = $paymentDetailsFactory->createApiDto($paymentDetails);
        $json = $serializer->serialize($output, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/customer/{customerId}/payment-methods/{paymentDetailsId}/default', name: 'api_v1.0_payment_details_default', methods: ['POST'])]
    public function makeDefault(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        DefaultPaymentManagerInterface $defaultPaymentManager,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $defaultPaymentManager->makePaymentDetailsDefault($customer, $paymentDetails);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/customer/{customerId}/payment-methods/{paymentDetailsId}', name: 'api_v1.0_payment_details_delete', methods: ['DELETE'])]
    public function deletePaymentDetails(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        DeleterInterface $deleter,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $deleter->delete($paymentDetails);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/customer/{customerId}/payment-methods', name: 'api_v1_payment_details_list', methods: ['GET'])]
    public function listPaymentDetails(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PaymentMethodsDataMapper $paymentMethodsFactory,
        SerializerInterface $serializer,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $paymentDetails = $paymentDetailsRepository->getPaymentCardForCustomer($customer);
        $dtos = array_map([$paymentMethodsFactory, 'createApiDto'], $paymentDetails);

        $list = new ListResponse();
        $list->setData($dtos);
        $list->setHasMore(false);

        $json = $serializer->serialize($list, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_ACCEPTED, json: true);
    }
}
