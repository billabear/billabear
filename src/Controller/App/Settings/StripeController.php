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

use App\Controller\ValidationErrorResponseTrait;
use App\Dto\Request\App\Settings\RegisterWebhook;
use App\Dto\Response\App\Settings\StripeImportView;
use App\Entity\GenericBackgroundTask;
use App\Entity\StripeImport;
use App\Enum\GenericTask;
use App\Enum\GenericTaskStatus;
use App\Factory\Settings\StripeImportFactory;
use App\Repository\GenericBackgroundTaskRepositoryInterface;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\StripeImportRepositoryInterface;
use Obol\Provider\ProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ADMIN')]
class StripeController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/settings/stripe/disable-billing', name: 'app_app_settings_stripe_disablestripebilling', methods: ['POST'])]
    public function disableStripeBilling(
        Request $request,
        GenericBackgroundTaskRepositoryInterface $genericBackgroundTaskRepository,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $settings = $settingsRepository->getDefaultSettings();
        $settings->getSystemSettings()->setUseStripeBilling(false);
        $settingsRepository->save($settings);

        $task = new GenericBackgroundTask();
        $task->setTask(GenericTask::CANCEL_STRIPE_BILLING);
        $task->setStatus(GenericTaskStatus::CREATED);
        $task->setCreatedAt(new \DateTime());
        $task->setUpdatedAt(new \DateTime());

        $genericBackgroundTaskRepository->save($task);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/settings/stripe/enable-billing', name: 'app_app_settings_stripe_enablestripebilling', methods: ['POST'])]
    public function enableStripeBilling(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $settings = $settingsRepository->getDefaultSettings();
        $settings->getSystemSettings()->setUseStripeBilling(true);
        $settingsRepository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/settings/stripe-import', name: 'app_app_settings_stripeimport_mainview', methods: ['GET'])]
    public function mainView(
        Request $request,
        StripeImportRepositoryInterface $stripeImportRepository,
        StripeImportFactory $importFactory,
        SerializerInterface $serializer,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $imports = $stripeImportRepository->getAll();
        $importDtos = array_map([$importFactory, 'createAppDto'], $imports);
        $viewDto = new StripeImportView();
        $viewDto->setStripeImports($importDtos);
        $viewDto->setUseStripeBilling($settingsRepository->getDefaultSettings()->getSystemSettings()->isUseStripeBilling());
        $viewDto->setWebhookUrl($settingsRepository->getDefaultSettings()->getSystemSettings()->getWebhookUrl());
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/stripe-import/start', name: 'app_app_settings_stripeimport_startimport', methods: ['POST'])]
    public function startImport(
        StripeImportRepositoryInterface $stripeImportRepository,
        StripeImportFactory $importFactory,
        SerializerInterface $serializer,
        SettingsRepositoryInterface $settingsRepository,
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

        $settings = $settingsRepository->getDefaultSettings();
        $settings->getOnboardingSettings()->setHasStripeImports(true);
        $settingsRepository->save($settings);

        return new JsonResponse($json, JsonResponse::HTTP_ACCEPTED, json: true);
    }

    #[Route('/app/settings/stripe/webhook/register', name: 'app_app_settings_stripe_registerwebhook', methods: ['POST'])]
    public function registerWebhook(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProviderInterface $provider,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        /** @var RegisterWebhook $dto */
        $dto = $serializer->deserialize($request->getContent(), RegisterWebhook::class, 'json');
        $errors = $validator->validate($dto);

        $errorResponse = $this->handleErrors($errors);
        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $response = $provider->webhook()->registerWebhook($dto->getUrl(), [
            'charge.failed',
            'charge.succeeded'], "Billabear's webhook");

        $settings = $settingsRepository->getDefaultSettings();
        $settings->getSystemSettings()->setWebhookExternalReference($response->getId());
        $settings->getSystemSettings()->setWebhookUrl($dto->getUrl());

        $settingsRepository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/settings/stripe/webhook/deregister', name: 'app_app_settings_stripe_deregisterwebhook', methods: ['POST'])]
    public function deregisterWebhook(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProviderInterface $provider,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $settings = $settingsRepository->getDefaultSettings();
        $response = $provider->webhook()->deregisterWebhook($settings->getSystemSettings()->getWebhookExternalReference());

        $settings->getSystemSettings()->setWebhookExternalReference(null);
        $settings->getSystemSettings()->setWebhookUrl(null);

        $settingsRepository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }
}
