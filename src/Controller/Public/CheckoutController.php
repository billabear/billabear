<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\Public;

use App\Customer\ExternalRegisterInterface;
use App\DataMappers\CheckoutDataMapper;
use App\DataMappers\CustomerDataMapper;
use App\Dto\Request\Public\CreateCustomerDto;
use App\Dto\Response\Portal\Checkout\CustomerCreation;
use App\Dto\Response\Portal\Checkout\ViewCheckout;
use App\Dto\Response\Portal\Quote\StripeInfo;
use App\Repository\CheckoutRepositoryInterface;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CheckoutController
{
    #[Route('/public/checkout/{slug}/view', name: 'app_public_checkout_readcheckout', methods: ['GET'])]
    public function readCheckout(
        Request $request,
        CheckoutRepositoryInterface $checkoutRepository,
        CheckoutDataMapper $checkoutDataMapper,
        SerializerInterface $serializer,
    ) {
        try {
            $checkout = $checkoutRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$checkout->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $checkoutDataMapper->createPublicDto($checkout);
        $viewDto = new ViewCheckout();
        $viewDto->setCheckout($dto);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/checkout/{slug}/customer', name: 'app_public_checkout_createcustomercheckout', methods: ['POST'])]
    public function createCustomerCheckout(
        Request $request,
        CustomerDataMapper $customerDataMapper,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        CheckoutRepositoryInterface $checkoutRepository,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        ExternalRegisterInterface $externalRegister,
        FrontendConfig $config,
    ) {
        try {
            $checkout = $checkoutRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$checkout->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var CreateCustomerDto $input */
        $input = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
        $customer = $customerDataMapper->createCustomer($input);
        $customer->setBrand($checkout->getBrandSettings()->getCode());
        $customer->setBrandSettings($checkout->getBrandSettings());
        $externalRegister->register($customer);
        $customerRepository->save($customer);

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($customer));
        $stripe->setKey($config->getApiInfo());
        $viewDto = new CustomerCreation();
        $viewDto->setStripe($stripe);

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
