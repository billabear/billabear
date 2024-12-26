<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Oauth;

use BillaBear\Integrations\Accounting\Messenger\EnableIntegration;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\Config;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;

class OauthManager
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
        $settings->getAccountingIntegration()->setIntegration($integrationName);
        $settings->getAccountingIntegration()->getOauthSettings()->setStateSecret($randomString);
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
        $sessionState = $settings->getAccountingIntegration()->getOauthSettings()->getStateSecret();

        if ($state !== $sessionState) {
            $this->getLogger()->critical('State mismatch', ['state' => $state, 'session_state' => $sessionState]);

            throw new \LogicException('State mismatch in oauth redirect request');
        }

        $integrationName = $request->get('integrationName');
        $integration = $this->integrationManager->getIntegration($integrationName);
        $provider = $this->getProvider($integration);

        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);
        $expiresAt = new \DateTime();
        $expiresAt->setTimestamp($accessToken->getExpires());

        $settings->getAccountingIntegration()->setIntegration($integrationName);
        $settings->getAccountingIntegration()->setEnabled(true);
        $settings->getAccountingIntegration()->getOauthSettings()->setAccessToken($accessToken->getToken());
        $settings->getAccountingIntegration()->getOauthSettings()->setRefreshToken($accessToken->getRefreshToken());
        $settings->getAccountingIntegration()->getOauthSettings()->setExpiresAt($expiresAt);
        $settings->getAccountingIntegration()->getOauthSettings()->setStateSecret(null);
        $settings->getAccountingIntegration()->setUpdatedAt(new \DateTime());

        $this->settingsRepository->save($settings);

        $redirectUrl = sprintf('%s/site/integration', $this->siteConfig->getSiteUrl());

        $this->messageBus->dispatch(new EnableIntegration());

        return new RedirectResponse($redirectUrl);
    }
}
