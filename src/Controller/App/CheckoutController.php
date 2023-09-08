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

namespace App\Controller\App;

use App\Checkout\CheckoutCreator;
use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\CheckoutDataMapper;
use App\Dto\Request\App\Checkout\CreateCheckout;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CheckoutController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/checkout/create', name: 'app_app_checkout_createcheckout', methods: ['POST'])]
    public function createCheckout(
        Request $request,
        CheckoutCreator $checkoutCreator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CheckoutDataMapper $checkoutDataMapper,
    ): Response {
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

        return new JsonResponse($json, json: true);
    }
}
