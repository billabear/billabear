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

use App\Dto\Request\Api\PaymentDetails\FrontendTokenComplete;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\PaymentDetails\FrontendToken;
use App\Entity\Customer;
use App\Factory\PaymentDetailsFactory;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Billing\PaymentDetails\DefaultPaymentManagerInterface;
use Parthenon\Billing\PaymentDetails\DeleterInterface;
use Parthenon\Billing\PaymentDetails\FrontendAddProcessorInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentDetailsController
{
    #[Route('/app/customer/{customerId}/payment-refund-details/frontend-payment-refund-token', name: 'app_payment_details_frontend_payment_token_start', methods: ['GET'])]
    public function startJsTokenAdd(
        Request $request,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        FrontendConfig $config,
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
        $dto->setApiInfo($config->getApiInfo());
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/customer/{customerId}/payment-refund-details/frontend-payment-refund-token', name: 'app_payment_details_frontend_payment_token_complete', methods: ['POST'])]
    public function finishFrontendAdd(
        Request $request,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PaymentDetailsFactory $paymentDetailsFactory,
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

    #[Route('/app/customer/{customerId}/payment-refund-details', name: 'app_payment_details_list', methods: ['GET'])]
    public function listCustomerPaymentMethods(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        PaymentDetailsFactory $paymentDetailsFactory,
        SerializerInterface $serializer,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $paymentDetails = $paymentDetailsRepository->getPaymentDetailsForCustomer($customer);
        $paymentDetailsDtos = array_map([$paymentDetailsFactory, 'createAppDto'], $paymentDetails);
        $listView = new ListResponse();
        $listView->setData($paymentDetailsDtos);

        $json = $serializer->serialize($listView, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/customer/{customerId}/payment-refund-details/{paymentDetailsId}/default', name: 'app_payment_details_default', methods: ['POST'])]
    public function makeDefault(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        DefaultPaymentManagerInterface $defaultPaymentManager,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var PaymentDetails $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $defaultPaymentManager->makePaymentDetailsDefault($customer, $paymentDetails);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/customer/{customerId}/payment-refund-details/{paymentDetailsId}', name: 'app_payment_details_delete', methods: ['DELETE'])]
    public function deletePaymentDetails(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        DeleterInterface $deleter,
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('customerId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var PaymentDetails $paymentDetails */
            $paymentDetails = $paymentDetailsRepository->findById($request->get('paymentDetailsId'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $deleter->delete($paymentDetails);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}
