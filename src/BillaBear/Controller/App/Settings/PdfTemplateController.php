<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Settings\BrandSettingsDataMapper;
use BillaBear\DataMappers\Settings\TemplateDataMapper;
use BillaBear\Dto\Request\App\Settings\PdfTemplates\UpdateGeneratorSettings;
use BillaBear\Dto\Request\App\Template\CreatePdfTemplate;
use BillaBear\Dto\Request\App\Template\PdfTemplate;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Dto\Response\App\Settings\ReadPdfGeneratorSettings;
use BillaBear\Dto\Response\App\Template\CreateTemplateView;
use BillaBear\Dto\Response\App\Template\TemplateView;
use BillaBear\Dummy\Data\ReceiptProvider;
use BillaBear\Entity\Template;
use BillaBear\Invoice\Formatter\InvoiceFormatterProvider;
use BillaBear\Pdf\PdfGeneratorType;
use BillaBear\Pdf\ReceiptPdfGenerator;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\TemplateRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Error\Error;

#[IsGranted('ROLE_ACCOUNT_MANAGER')]
class PdfTemplateController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/settings/template', name: 'app_settings_template_list', methods: ['GET'])]
    public function getTemplateList(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateDataMapper $factory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to read notification settings');
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

    #[Route('/app/settings/template/{id}/view', name: 'app_settings_template_view', methods: ['GET'])]
    public function getTemplate(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateDataMapper $factory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to read template', ['pdf_template_id' => $request->get('id')]);
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

    #[Route('/app/settings/template/create', name: 'billabear_app_settings_pdftemplate_readcreatetemplate', methods: ['GET'])]
    public function readCreateTemplate(
        Request $request,
        BrandSettingsRepositoryInterface $settingsRepository,
        BrandSettingsDataMapper $brandSettingsDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to read create template');
        $brands = $settingsRepository->getAll();
        $brandDtos = array_map([$brandSettingsDataMapper, 'createAppDto'], $brands);

        $view = new CreateTemplateView();
        $view->setBrands($brandDtos);
        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/template/create', name: 'billabear_app_settings_pdftemplate_writecreatetemplate', methods: ['POST'])]
    public function writeCreateTemplate(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateDataMapper $templateDataMapper,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        $this->getLogger()->info('Received request to write create template');
        /** @var CreatePdfTemplate $createTemplate */
        $createTemplate = $serializer->deserialize($request->getContent(), CreatePdfTemplate::class, 'json');
        $errors = $validator->validate($createTemplate);
        $response = $this->handleErrors($errors);
        if ($response instanceof Response) {
            return $response;
        }
        $entity = $templateDataMapper->createEntity($createTemplate);
        $templateRepository->save($entity);
        $appDto = $templateDataMapper->createAppDto($entity);
        $json = $serializer->serialize($appDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/template/{id}/update', name: 'app_settings_template_update', methods: ['POST'])]
    public function updateTemplate(
        Request $request,
        TemplateRepositoryInterface $templateRepository,
        TemplateDataMapper $factory,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to update template', ['pdf_template' => $request->get('id')]);
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
        $this->getLogger()->info('Received request to download receipt template', ['pdf_template' => $request->get('id')]);
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
        InvoiceFormatterProvider $invoiceFormatterProvider,
        ReceiptProvider $provider,
    ): Response {
        $this->getLogger()->info('Received request to download invoice template', ['pdf_template' => $request->get('id')]);
        try {
            /** @var Template $template */
            $template = $templateRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $invoice = $provider->getInvoice();
        $generator = $invoiceFormatterProvider->getFormatter($invoice->getCustomer());
        try {
            $pdf = $generator->generate($invoice);
        } catch (Error $e) {
            $data = ['raw_message' => $e->getRawMessage()];

            return new JsonResponse($data, JsonResponse::HTTP_BAD_REQUEST);
        }
        $tmpFile = tempnam('/tmp', 'pdf');
        file_put_contents($tmpFile, $pdf);

        $response = new BinaryFileResponse($tmpFile);
        $filename = $generator->filename($invoice);

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
        $this->getLogger()->info('Received request to read pdf generator settings');

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
        $this->getLogger()->info('Received request to update pdf generator settings');
        /** @var UpdateGeneratorSettings $dto */
        $dto = $serializer->deserialize($request->getContent(), UpdateGeneratorSettings::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response instanceof Response) {
            return $response;
        }
        $settings = $settingsRepository->getDefaultSettings();

        $settings->getSystemSettings()->setPdfGenerator(PdfGeneratorType::fromName($dto->generator));
        $settings->getSystemSettings()->setPdfBin($dto->bin);
        $settings->getSystemSettings()->setPdfApiKey($dto->apiKey);
        $settings->getSystemSettings()->setPdfTmpDir($dto->tmpDir);

        $settingsRepository->save($settings);

        return new JsonResponse(['success' => true]);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
