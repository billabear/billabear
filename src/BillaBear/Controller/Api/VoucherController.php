<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\Dto\Request\Api\Vouchers\ApplyVoucher;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\VoucherRepositoryInterface;
use BillaBear\Voucher\VoucherApplier;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VoucherController
{
    use ValidationErrorResponseTrait;
    use LoggerAwareTrait;

    #[Route('/api/v1/customer/{id}/voucher', name: 'app_api_voucher_apply_code', methods: ['POST'])]
    public function applyCode(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        VoucherRepositoryInterface $voucherRepository,
        CustomerRepositoryInterface $customerRepository,
        VoucherApplier $applier,
    ): JsonResponse {
        $this->getLogger()->info('Received API request apply voucher to customer', ['customer_id' => $request->get('id')]);
        try {
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
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

        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }
}
