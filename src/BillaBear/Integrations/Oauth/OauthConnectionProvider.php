<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Oauth;

use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;

class OauthConnectionProvider
{
    use ProviderTrait;
    use LoggerAwareTrait;

    public function __construct(
        private SettingsRepositoryInterface $settingsRepository,
        private IntegrationManager $integrationManager,
    ) {
    }

    public function getAccessToken(): string
    {
        $settings = $this->settingsRepository->getDefaultSettings();
        $integration = $this->integrationManager->getIntegration($settings->getAccountingIntegration()->getIntegration());

        if (AuthenticationType::OAUTH !== $integration->getAuthenticationType()) {
            throw new UnsupportedFeatureException('Integration '.$integration->getName().' does not support Oauth');
        }

        $now = new \DateTime();
        $expiresAt = $settings->getAccountingIntegration()->getOauthSettings()->getExpiresAt();
        if ($now > $expiresAt) {
            $this->getLogger()->info('Refreshing access token for integration ', ['integration' => $integration->getName()]);

            $provider = $this->getProvider($integration);
            $accessToken = $provider->getAccessToken('refresh_token', ['refresh_token' => $settings->getAccountingIntegration()->getOauthSettings()->getRefreshToken()]);

            $expiresAt = new \DateTime();
            $expiresAt->setTimestamp($accessToken->getExpires());

            $settings->getAccountingIntegration()->getOauthSettings()->setAccessToken($accessToken->getToken());
            $settings->getAccountingIntegration()->getOauthSettings()->setRefreshToken($accessToken->getRefreshToken());
            $settings->getAccountingIntegration()->getOauthSettings()->setExpiresAt($expiresAt);

            $this->settingsRepository->save($settings);
        }

        return $settings->getAccountingIntegration()->getOauthSettings()->getAccessToken();
    }
}
