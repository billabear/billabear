<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Oauth;

use BillaBear\Exception\Integrations\UnsupportedFeatureException;
use BillaBear\Integrations\AuthenticationType;
use BillaBear\Integrations\IntegrationInterface;
use BillaBear\Integrations\IntegrationManager;
use BillaBear\Integrations\IntegrationType;
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

    public function getAccessToken(IntegrationInterface $integration): string
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (AuthenticationType::OAUTH !== $integration->getAuthenticationType()) {
            throw new UnsupportedFeatureException('Integration '.$integration->getName().' does not support Oauth');
        }

        $now = new \DateTime();
        $integrationSettings = match ($integration->getType()) {
            IntegrationType::ACCOUNTING => $settings->getAccountingIntegration(),
            IntegrationType::CRM => $settings->getCrmIntegration(),
            default => throw new UnsupportedFeatureException('Integration type '.$integration->getType()->value.' not supported'),
        };

        $expiresAt = $integrationSettings->getOauthSettings()->getExpiresAt();
        if ($now > $expiresAt) {
            $this->getLogger()->info('Refreshing access token for integration ', ['integration' => $integration->getName()]);

            $provider = $this->getProvider($integration);
            $accessToken = $provider->getAccessToken('refresh_token', ['refresh_token' => $integrationSettings->getOauthSettings()->getRefreshToken()]);

            $expiresAt = new \DateTime();
            $expiresAt->setTimestamp($accessToken->getExpires());

            $integrationSettings->getOauthSettings()->setAccessToken($accessToken->getToken());
            $integrationSettings->getOauthSettings()->setRefreshToken($accessToken->getRefreshToken());
            $integrationSettings->getOauthSettings()->setExpiresAt($expiresAt);

            $this->settingsRepository->save($settings);
        }

        return $integrationSettings->getOauthSettings()->getAccessToken();
    }
}
