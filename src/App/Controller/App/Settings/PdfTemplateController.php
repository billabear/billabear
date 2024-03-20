<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Settings;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\Settings\TemplateDataMapper;
use App\Dto\Request\App\Settings\PdfTemplates\UpdateGeneratorSettings;
use App\Dto\Request\App\Template\PdfTemplate;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Settings\ReadPdfGeneratorSettings;
use App\Dto\Response\App\Template\TemplateView;
use App\Dummy\Data\ReceiptProvider;
use App\Entity\Template;
use App\Enum\PdfGeneratorType;
use App\Pdf\InvoicePdfGenerator;
use App\Pdf\ReceiptPdfGenerator;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\TemplateRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Error\Error;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class PdfTemplateController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/settings/template', name: 'app_settings_template_list', methods: ['GET'])]
    public function getTemplateList(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateDataMapper $factory,
        SerializerInterface $serializer
    ): Response {
        $templates = $templateRepository->getByBrand($request->get('brand', 'default'));
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
        TemplateDataMapper $factory,
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
        TemplateDataMapper $factory,
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
        try {
            $pdf = $generator->generate($receipt);
        } catch (Error $e) {
            $data = ['raw_message' => $e->getRawMessage()];

            return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
        }
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

    #[Route('/app/settings/template/{id}/invoice-download', name: 'app_settings_template_invocie_download', methods: ['GET'])]
    public function downloadInvoice(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        InvoicePdfGenerator $generator,
        ReceiptProvider $provider,
    ): Response {
        try {
            /** @var Template $template */
            $template = $templateRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $invoice = $provider->getInvoice();
        try {
            $pdf = $generator->generate($invoice);
        } catch (Error $e) {
            $data = ['raw_message' => $e->getRawMessage()];

            return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
        }
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

    #[IsGranted('ROLE_DEVELOPER')]
    #[Route('/app/settings/pdf-generator', name: 'app_app_settings_pdftemplate_readgeneratorsettings', methods: ['GET'])]
    public function readGeneratorSettings(
        Request $request,
        SerializerInterface $serializer,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $settings = $settingsRepository->getDefaultSettings();

        $dto = new ReadPdfGeneratorSettings();
        $dto->setGenerator($settings->getSystemSettings()->getPdfGenerator()?->value ?? PdfGeneratorType::MPDF->value);
        $dto->setBin($settings->getSystemSettings()->getPdfBin());
        $dto->setApiKey($settings->getSystemSettings()->getPdfApiKey());
        $dto->setTmpDir($settings->getSystemSettings()->getPdfTmpDir());

        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_DEVELOPER')]
    #[Route('/app/settings/pdf-generator', name: 'app_app_settings_pdftemplate_updategeneratorsettings', methods: ['POST'])]
    public function updateGeneratorSettings(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $dto = $serializer->deserialize($request->getContent(), UpdateGeneratorSettings::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }
        $settings = $settingsRepository->getDefaultSettings();

        $settings->getSystemSettings()->setPdfGenerator(PdfGeneratorType::fromName($dto->getGenerator()));
        $settings->getSystemSettings()->setPdfBin($dto->getBin());
        $settings->getSystemSettings()->setPdfApiKey($dto->getApiKey());
        $settings->getSystemSettings()->setPdfTmpDir($dto->getTmpDir());

        $settingsRepository->save($settings);

        return new JsonResponse(['success' => true]);
    }
}
