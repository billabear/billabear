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

namespace App\Controller\Api;

use App\Dto\Request\Api\CreateProduct;
use App\Factory\ProductFactory;
use Parthenon\Billing\Obol\ProductRegisterInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController
{
    #[Route('/api/v1.0/product', name: 'api_v1.0_product_create', methods: ['POST'])]
    public function createProduct(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProductFactory $productFactory,
        ProductRegisterInterface $productRegister,
        ProductRepositoryInterface $productRepository,
    ): Response {
        $dto = $serializer->deserialize($request->getContent(), CreateProduct::class, 'json');
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

        $product = $productFactory->createFromApiCreate($dto);
        if (!$product->hasExternalReference()) {
            $product = $productRegister->registerProduct($product);
        }
        $productRepository->save($product);

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }
}
