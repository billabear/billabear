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

use App\Controller\ValidationErrorResponseTrait;
use App\Dto\Request\App\Voucher\CreateVoucher;
use App\Factory\VoucherFactory;
use App\Repository\VoucherRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VoucherController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/voucher', name: 'app_app_voucher_createvoucher', methods: ['POST'])]
    public function createVoucher(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        VoucherRepositoryInterface $voucherRepository,
        VoucherFactory $voucherFactory,
    ) {
        $createVoucher = $serializer->deserialize($request->getContent(), CreateVoucher::class, 'json');
        $errors = $validator->validate($createVoucher);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $voucherFactory->createEntity($createVoucher);
        $voucherRepository->save($entity);
        $dto = $voucherFactory->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }
}
