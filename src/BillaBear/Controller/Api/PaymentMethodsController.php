<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\DataMappers\PaymentMethodsDataMapper;
use BillaBear\Dto\Request\Api\PaymentDetails\FrontendTokenComplete;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Dto\Response\Api\PaymentDetails\FrontendToken;
use BillaBear\Entity\Customer;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\PaymentMethod\DefaultPaymentManagerInterface;
use Parthenon\Billing\PaymentMethod\DeleterInterface;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentMethodsController
{
    use LoggerAwareTrait;

    #[Route('/api/v1/customer/{customerId}/payment-methods/frontend-payment-token', name: 'api_v1.0_payment_details_frontend_payment_token_start', methods: ['GET'])]
    public function startJsTokenAdd(
        Request $request,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to start frontend payment token', ['customer_id' => $request->get('customerId')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
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
        $this->getLogger()->info('Received request to finish frontend payment token', ['customer_id' => $request->get('customerId')]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
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
            ], Response::HTTP_BAD_REQUEST);
        }

        $paymentDetails = $addCardByTokenDriver->createPaymentDetailsFromToken($customer, $dto->getToken());

        $output = $paymentDetailsFactory->createApiDto($paymentDetails);
        $json = $serializer->serialize($output, 'json');

        return new JsonResponse($json, Response::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/customer/{customerId}/payment-methods/{paymentDetailsId}/default', name: 'api_v1_customer_payment_details_default', methods: ['POST'])]
    #[Route('/api/v1/payment-methods/{paymentDetailsId}/default', name: 'api_v1_payment_details_default', methods: ['POST'])]
    public function makeDefault(
        Request $request,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        DefaultPaymentManagerInterface $defaultPaymentManager,
    ): Response {
        $this->getLogger()->info('Received request to make payment details default', [
            'payment_details_id' => $request->get('paymentDetailsId'),
        ]);
        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $defaultPaymentManager->makePaymentDetailsDefault($paymentDetails->getCustomer(), $paymentDetails);

        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/payment-methods/{paymentDetailsId}', name: 'api_v1_payment_details_view', methods: ['GET'])]
    public function viewPaymentDetails(
        Request $request,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PaymentMethodsDataMapper $paymentMethodsDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to make payment details default', [
            'payment_details_id' => $request->get('paymentDetailsId'),
        ]);
        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
        $dto = $paymentMethodsDataMapper->createApiDto($paymentDetails);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/v1/customer/{customerId}/payment-methods/{paymentDetailsId}', name: 'api_v1_customer_payment_details_delete', methods: ['DELETE'])]
    #[Route('/api/v1/payment-methods/{paymentDetailsId}', name: 'api_v1_payment_details_delete', methods: ['DELETE'])]
    public function deletePaymentDetails(
        Request $request,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        DeleterInterface $deleter,
    ): Response {
        $this->getLogger()->info('Received request to delete payment details', [
            'payment_details_id' => $request->get('paymentDetailsId'),
        ]);

        try {
            /** @var PaymentCard $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $deleter->delete($paymentDetails);

        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }

    #[Route('/api/v1/customer/{customerId}/payment-methods', name: 'api_v1_payment_details_list', methods: ['GET'])]
    public function listPaymentDetails(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentCardRepositoryInterface $paymentDetailsRepository,
        PaymentMethodsDataMapper $paymentMethodsFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to list customer payment methods', [
            'customer_id' => $request->get('customerId'),
        ]);
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $paymentDetails = $paymentDetailsRepository->getPaymentCardForCustomer($customer);
        $dtos = array_map([$paymentMethodsFactory, 'createApiDto'], $paymentDetails);

        $list = new ListResponse();
        $list->setData($dtos);
        $list->setHasMore(false);

        $json = $serializer->serialize($list, 'json');

        return new JsonResponse($json, Response::HTTP_ACCEPTED, json: true);
    }
}
