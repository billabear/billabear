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

namespace App\Controller\App\Settings;

use App\Dto\Response\App\Settings\StripeImportView;
use App\Entity\StripeImport;
use App\Factory\Settings\StripeImportFactory;
use App\Repository\StripeImportRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class StripeImportController
{
    #[Route('/app/settings/stripe-import', name: 'app_app_settings_stripeimport_mainview', methods: ['GET'])]
    public function mainView(
        Request $request,
        StripeImportRepositoryInterface $stripeImportRepository,
        StripeImportFactory $importFactory,
        SerializerInterface $serializer,
    ): Response {
        $imports = $stripeImportRepository->getAll();
        $importDtos = array_map([$importFactory, 'createAppDto'], $imports);
        $viewDto = new StripeImportView();
        $viewDto->setStripeImports($importDtos);
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/stripe-import/start', name: 'app_app_settings_stripeimport_startimport', methods: ['POST'])]
    public function startImport(
        StripeImportRepositoryInterface $stripeImportRepository,
        StripeImportFactory $importFactory,
        SerializerInterface $serializer,
    ): Response {
        $stripeImport = $stripeImportRepository->findActive();

        if ($stripeImport) {
            return new JsonResponse([], JsonResponse::HTTP_CONFLICT);
        }

        $stripeImport = new StripeImport();
        $stripeImport->setState('started');
        $stripeImport->setLastId(null);
        $stripeImport->setComplete(false);
        $stripeImport->setUpdatedAt(new \DateTime());
        $stripeImport->setCreatedAt(new \DateTime());

        $stripeImportRepository->save($stripeImport);
        $dto = $importFactory->createAppDto($stripeImport);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_ACCEPTED, json: true);
    }
}
