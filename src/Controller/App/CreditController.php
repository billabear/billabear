<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Credit\CreditAdjustmentRecorder;
use App\DataMappers\CreditFactory;
use App\Dto\Request\App\CreditAdjustment\CreateCreditAdjustment;
use App\Entity\Customer;
use App\Repository\CustomerRepositoryInterface;
use App\User\UserProvider;
use Brick\Money\Money;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreditController
{
    #[Route('/app/customer/{id}/credit', name: 'app_customer_credit_create', methods: ['POST'])]
    public function createCredit(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CreditFactory $factory,
        UserProvider $userProvider,
        CreditAdjustmentRecorder $creditAdjustmentRecorder
    ): Response {
        try {
            /** @var Customer $customer */
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var CreateCreditAdjustment $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateCreditAdjustment::class, 'json');
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

        $billingAdmin = $userProvider->getUser();
        $credit = $creditAdjustmentRecorder->createRecord($dto->getType(), $customer, Money::ofMinor($dto->getAmount(), strtoupper($dto->getCurrency())), $dto->getReason(), $billingAdmin);

        $customer->addCreditAsMoney($credit->asMoney());
        $customerRepository->save($customer);

        $creditDto = $factory->createAppDto($credit);
        $jsonResponse = $serializer->serialize($creditDto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }
}
