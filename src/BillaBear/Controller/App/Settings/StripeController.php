<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Settings;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\Settings\StripeImportDataMapper;
use BillaBear\Dto\Request\App\Settings\RegisterWebhook;
use BillaBear\Dto\Request\App\Settings\Stripe\SendConfig;
use BillaBear\Dto\Response\App\Settings\StripeImportView;
use BillaBear\Entity\GenericBackgroundTask;
use BillaBear\Entity\StripeImport;
use BillaBear\Entity\User;
use BillaBear\Enum\GenericTask;
use BillaBear\Enum\GenericTaskStatus;
use BillaBear\Repository\GenericBackgroundTaskRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\StripeImportRepositoryInterface;
use Obol\Provider\ProviderInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[IsGranted('ROLE_ADMIN')]
class StripeController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/settings/stripe/disable-billing', name: 'app_app_settings_stripe_disablestripebilling', methods: ['POST'])]
    public function disableStripeBilling(
        Request $request,
        GenericBackgroundTaskRepositoryInterface $genericBackgroundTaskRepository,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->getLogger()->info('Request to disable Stripe Billing');
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

    #[Route('/app/settings/stripe/set-config', name: 'billabear_app_settings_stripe_sendconfig', methods: ['POST'])]
    public function sendConfig(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->getLogger()->info('Request to set Stripe config');
        $dto = $serializer->deserialize($request->getContent(), SendConfig::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);
        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $settings = $settingsRepository->getDefaultSettings();
        $settings->getSystemSettings()->setStripePrivateKey($dto->getPrivateKey());
        $settings->getSystemSettings()->setStripePublicKey($dto->getPublicKey());
        $settingsRepository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/settings/stripe/enable-billing', name: 'app_app_settings_stripe_enablestripebilling', methods: ['POST'])]
    public function enableStripeBilling(
        Request $request,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->getLogger()->info('Request to enable Stripe billing');
        $settings = $settingsRepository->getDefaultSettings();
        $settings->getSystemSettings()->setUseStripeBilling(true);
        $settingsRepository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/settings/stripe-import', name: 'app_app_settings_stripeimport_mainview', methods: ['GET'])]
    public function mainView(
        Request $request,
        StripeImportRepositoryInterface $stripeImportRepository,
        StripeImportDataMapper $importFactory,
        SerializerInterface $serializer,
        SettingsRepositoryInterface $settingsRepository,
        #[Autowire('%parthenon_billing_payments_obol_config%')]
        $obolConfig,
    ): Response {
        $this->getLogger()->info('Request to view stripe import list');

        $imports = $stripeImportRepository->getAll();
        $importDtos = array_map([$importFactory, 'createAppDto'], $imports);
        $viewDto = new StripeImportView();
        $viewDto->setStripeImports($importDtos);
        $viewDto->setHasObolConfig(isset($obolConfig['api_key']) && !empty($obolConfig['api_key']));
        $viewDto->setUseStripeBilling($settingsRepository->getDefaultSettings()->getSystemSettings()->isUseStripeBilling());
        $viewDto->setWebhookUrl($settingsRepository->getDefaultSettings()->getSystemSettings()->getWebhookUrl());
        $viewDto->setStripePrivateKey($settingsRepository->getDefaultSettings()->getSystemSettings()->getStripePrivateKey());
        $viewDto->setStripePublicKey($settingsRepository->getDefaultSettings()->getSystemSettings()->getStripePublicKey());
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/settings/stripe-import/start', name: 'app_app_settings_stripeimport_startimport', methods: ['POST'])]
    public function startImport(
        StripeImportRepositoryInterface $stripeImportRepository,
        StripeImportDataMapper $importFactory,
        SerializerInterface $serializer,
        SettingsRepositoryInterface $settingsRepository,
        #[CurrentUser]
        User $user,
    ): Response {
        $this->getLogger()->info('Request to start stripe import');
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
        $stripeImport->setCreatedBy($user);

        $stripeImportRepository->save($stripeImport);
        $dto = $importFactory->createAppDto($stripeImport);
        $json = $serializer->serialize($dto, 'json');

        $settings = $settingsRepository->getDefaultSettings();
        $settings->getOnboardingSettings()->setHasStripeImports(true);
        $settingsRepository->save($settings);

        return new JsonResponse($json, JsonResponse::HTTP_ACCEPTED, json: true);
    }

    #[Route('/app/settings/stripe-import/dismiss', name: 'app_app_settings_stripeimport_dismiss', methods: ['POST'])]
    public function dismissStripeImports(
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Request to dismiss stripe import');
        $settings = $settingsRepository->getDefaultSettings();
        $settings->getOnboardingSettings()->setHasStripeImports(true);
        $settingsRepository->save($settings);

        return new JsonResponse([]);
    }

    #[Route('/app/settings/stripe-import/{id}/view', name: 'app_app_settings_stripeimport_viewimport', methods: ['GET'])]
    public function viewImport(
        Request $request,
        StripeImportRepositoryInterface $stripeImportRepository,
        StripeImportDataMapper $importFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Request to view stripe import', ['stripe_import_id' => $request->get('id')]);

        try {
            $stripeImport = $stripeImportRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], status: JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $importFactory->createAppDto($stripeImport);
        $jsonData = $serializer->serialize($dto, 'json');

        return new JsonResponse($jsonData, json: true);
    }

    #[Route('/app/settings/stripe/webhook/register', name: 'app_app_settings_stripe_registerwebhook', methods: ['POST'])]
    public function registerWebhook(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProviderInterface $provider,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Request to register stripe webhook');
        /** @var RegisterWebhook $dto */
        $dto = $serializer->deserialize($request->getContent(), RegisterWebhook::class, 'json');
        $errors = $validator->validate($dto);

        $errorResponse = $this->handleErrors($errors);
        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $response = $provider->webhook()->registerWebhook($dto->url, [
            'charge.failed',
            'charge.succeeded'], "Billabear's webhook");

        $settings = $settingsRepository->getDefaultSettings();
        $settings->getSystemSettings()->setWebhookExternalReference($response->getId());
        $settings->getSystemSettings()->setWebhookUrl($dto->url);
        $settings->getSystemSettings()->setWebhookSecret($response->getSecret());

        $settingsRepository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/settings/stripe/webhook/deregister', name: 'app_app_settings_stripe_deregisterwebhook', methods: ['POST'])]
    public function deregisterWebhook(
        ProviderInterface $provider,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        $this->getLogger()->info('Request to deregister stripe webhook');
        $settings = $settingsRepository->getDefaultSettings();

        if (null === $settings->getSystemSettings()->getWebhookExternalReference()) {
            return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
        }
        $provider->webhook()->deregisterWebhook($settings->getSystemSettings()->getWebhookExternalReference());

        $settings->getSystemSettings()->setWebhookExternalReference(null);
        $settings->getSystemSettings()->setWebhookUrl(null);

        $settingsRepository->save($settings);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
