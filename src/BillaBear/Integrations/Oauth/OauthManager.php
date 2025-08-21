<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Oauth;

use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Integrations\IntegrationType;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\Config;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;

class OauthManager implements OauthManagerInterface
{
    use LoggerAwareTrait;
    use ProviderTrait;

    public function __construct(
        private IntegrationManager $integrationManager,
        private RequestStack $requestStack,
        private AuthorizationUrlProviderInterface $redirectUrlProvider,
        private Config $siteConfig,
        private SettingsRepositoryInterface $settingsRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function sendToIntegration(string $integrationName): RedirectResponse
    {
        $integration = $this->integrationManager->getIntegration($integrationName);

        if (AuthenticationType::OAUTH !== $integration->getAuthenticationType()) {
            $this->getLogger()->critical('Integration does not support Oauth', ['integration_name' => $integrationName]);

            throw new \LogicException('Integration does not support Oauth');
        }

        $provider = $this->getProvider($integration);
        $randomString = bin2hex(random_bytes(16));
        $state = ['random' => $randomString];

        $settings = $this->settingsRepository->getDefaultSettings();
        $integrationSettings = match ($integration->getType()) {
            IntegrationType::ACCOUNTING => $settings->getAccountingIntegration(),
            IntegrationType::CRM => $settings->getCrmIntegration(),
            default => throw new \Exception("Unsupported integration type: {$integration->getType()}"),
        };

        $integrationSettings->setIntegration($integrationName);
        $integrationSettings->getOauthSettings()->setStateSecret($randomString);
        $this->settingsRepository->save($settings);

        $authorizationUrl = $this->redirectUrlProvider->getAuthUrl($provider, $integration->getOauthConfig()->scope, $state);

        $this->requestStack->getCurrentRequest()->getSession()->set('oauth_state', $randomString);

        return new RedirectResponse($authorizationUrl);
    }

    public function handleRedirect(Request $request): RedirectResponse
    {
        $code = $request->get('code');

        if (!$code) {
            $this->getLogger()->critical('No code in request', ['request' => $request->query->all()]);

            throw new \LogicException('No code in oauth redirect request');
        }

        $rawState = $request->get('state');
        $stateData = json_decode(base64_decode($rawState), true);
        $state = $stateData['random'];
        $settings = $this->settingsRepository->getDefaultSettings();

        $integrationName = $request->get('integrationName');
        $integration = $this->integrationManager->getIntegration($integrationName);
        $provider = $this->getProvider($integration);

        $integrationSettings = match ($integration->getType()) {
            IntegrationType::ACCOUNTING => $settings->getAccountingIntegration(),
            IntegrationType::CRM => $settings->getCrmIntegration(),
            default => throw new \Exception("Unsupported integration type: {$integration->getType()}"),
        };

        $sessionState = $integrationSettings->getOauthSettings()->getStateSecret();

        if ($state !== $sessionState) {
            $this->getLogger()->critical('State mismatch', ['state' => $state, 'session_state' => $sessionState]);

            throw new \LogicException('State mismatch in oauth redirect request');
        }
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);
        $expiresAt = new \DateTime();
        $expiresAt->setTimestamp($accessToken->getExpires());

        $integrationSettings->setIntegration($integrationName);
        $integrationSettings->setEnabled(true);
        $integrationSettings->getOauthSettings()->setAccessToken($accessToken->getToken());
        $integrationSettings->getOauthSettings()->setRefreshToken($accessToken->getRefreshToken());
        $integrationSettings->getOauthSettings()->setExpiresAt($expiresAt);
        $integrationSettings->getOauthSettings()->setStateSecret(null);

        $this->settingsRepository->save($settings);

        $redirectUrl = match ($integration->getType()) {
            IntegrationType::ACCOUNTING => sprintf('%s/site/integrations/accounting', $this->siteConfig->getSiteUrl()),
            IntegrationType::CRM => sprintf('%s/site/integrations/crm', $this->siteConfig->getSiteUrl()),
            default => sprintf('%s/site/home', $this->siteConfig->getSiteUrl()),
        };

        return new RedirectResponse($redirectUrl);
    }
}
