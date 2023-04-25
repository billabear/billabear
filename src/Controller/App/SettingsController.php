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

use App\Dto\Request\App\Template\PdfTemplate;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Template\TemplateView;
use App\Dummy\Data\ReceiptProvider;
use App\Entity\Template;
use App\Factory\TemplateFactory;
use App\Pdf\ReceiptPdfGenerator;
use App\Repository\TemplateRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SettingsController
{
    #[Route('/app/settings/template', name: 'app_settings_template_list', methods: ['GET'])]
    public function getTemplateList(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateFactory $factory,
        SerializerInterface $serializer
    ): Response {
        $templates = $templateRepository->getByGroup($request->get('group', 'default'));
        $dtos = array_map([$factory, 'createAppDto'], $templates);

        $listResponse = new ListResponse();
        $listResponse->setHasMore(false);
        $listResponse->setData($dtos);
        $listResponse->setLastKey(null);
        $listResponse->setFirstKey(null);

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/template/{id}', name: 'app_settings_template_view', methods: ['GET'])]
    public function getTemplate(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateFactory $factory,
        SerializerInterface $serializer,
    ): Response {
        try {
            /** @var Template $template */
            $template = $templateRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = new TemplateView();
        $dto->setTemplate($factory->createAppDto($template));
        $dto->setContent($template->getContent());

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/template/{id}', name: 'app_settings_template_update', methods: ['POST'])]
    public function updateTemplate(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateFactory $factory,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        try {
            /** @var Template $template */
            $template = $templateRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        /** @var PdfTemplate $dto */
        $dto = $serializer->deserialize($request->getContent(), PdfTemplate::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $template->setContent($dto->getContent());

        $templateRepository->save($template);

        $dto = new TemplateView();
        $dto->setTemplate($factory->createAppDto($template));
        $dto->setContent($template->getContent());

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/template/{id}/receipt-download', name: 'app_settings_template_receipt_download', methods: ['GET'])]
    public function downloadReceipt(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        ReceiptPdfGenerator $generator,
        ReceiptProvider $provider,
    ): Response {
        try {
            /** @var Template $template */
            $template = $templateRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $receipt = $provider->getDummyReceipt();
        $pdf = $generator->generate($receipt);
        $tmpFile = tempnam('/tmp', 'pdf');
        file_put_contents($tmpFile, $pdf);

        $response = new BinaryFileResponse($tmpFile);
        $filename = 'dummy.pdf';

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
