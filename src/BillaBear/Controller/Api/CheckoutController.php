<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Checkout\CheckoutCreator;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\CheckoutDataMapper;
use BillaBear\Dto\Request\Api\Checkout\CreateCheckout;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CheckoutController
{
    use ValidationErrorResponseTrait;
    use LoggerAwareTrait;

    #[Route('/api/v1/checkout', name: 'app_api_checkout_create_checkout', methods: ['POST'])]
    public function createCheckout(
        Request $request,
        CheckoutCreator $checkoutCreator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CheckoutDataMapper $checkoutDataMapper,
    ): Response {
        $this->getLogger()->info('Received an API request to create a checkout');
        /** @var CreateCheckout $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateCheckout::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response) {
            return $response;
        }

        $quote = $checkoutCreator->createCheckout($dto);
        $dto = $checkoutDataMapper->createAppDto($quote);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }
}
