<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Api;

use App\Controller\ValidationErrorResponseTrait;
use App\Dto\Request\Api\Vouchers\ApplyVoucher;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\VoucherRepositoryInterface;
use App\Voucher\VoucherApplier;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VoucherController
{
    use ValidationErrorResponseTrait;

    #[Route('/api/v1/customer/{id}/voucher', name: 'app_api_voucher_applycode', methods: ['POST'])]
    public function applyCode(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        VoucherRepositoryInterface $voucherRepository,
        CustomerRepositoryInterface $customerRepository,
        VoucherApplier $applier,
    ) {
        try {
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var ApplyVoucher $dto */
        $dto = $serializer->deserialize($request->getContent(), ApplyVoucher::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof JsonResponse) {
            return $errorResponse;
        }
        $voucher = $voucherRepository->getActiveByCode($dto->getCode());
        $applier->applyVoucherToCustomer($customer, $voucher);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}
