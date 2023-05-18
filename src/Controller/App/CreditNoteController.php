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

use App\Dto\Request\App\CreditNote\CreateCreditNote;
use App\Entity\CreditNote;
use App\Factory\CreditNoteFactory;
use App\Repository\CreditNoteRepositoryInterface;
use App\Repository\CustomerRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreditNoteController
{
    #[Route('/app/customer/{id}/credit-note', name: 'app_customer_credit_note_create', methods: ['POST'])]
    public function createCreditNote(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CreditNoteFactory $factory,
        CreditNoteRepositoryInterface $repository,
        Security $security,
    ): Response {
        try {
            $customer = $customerRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $serializer->deserialize($request->getContent(), CreateCreditNote::class, 'json');
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

        $creditNote = $factory->createEntity($dto, $customer);
        $billingAdmin = $security->getUser();
        $creditNote->setBillingAdmin($billingAdmin);
        $creditNote->setCreationType(CreditNote::CREATION_TYPE_MANUALLY);

        $repository->save($creditNote);
        $creditNoteDto = $factory->createAppDto($creditNote);
        $jsonResponse = $serializer->serialize($creditNoteDto, 'json');

        return new JsonResponse($jsonResponse, JsonResponse::HTTP_CREATED, json: true);
    }
}
