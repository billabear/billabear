<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Settings;

use BillaBear\Entity\Settings\NotificationSettings as Entity;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback('validate')]
readonly class NotificationSettings
{
    public function __construct(
        #[Assert\Type('boolean')]
        #[SerializedName('send_customer_notifications')]
        public ?bool $sendCustomerNotifications = null,
        #[Assert\Choice(choices: Entity::EMSP_CHOICES)]
        #[Assert\NotBlank]
        public ?string $emsp = null,
        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('emsp_api_key')]
        public ?string $emspApiKey = null,
        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('emsp_api_url')]
        public ?string $emspApiUrl = null,
        #[Assert\NotBlank(allowNull: true)]
        #[SerializedName('emsp_domain')]
        public ?string $emspDomain = null,
        #[Assert\Email]
        #[Assert\NotBlank]
        #[SerializedName('default_outgoing_email')]
        public ?string $defaultOutgoingEmail = null,
    ) {
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (empty($this->emsp) || Entity::EMSP_SYSTEM === $this->emsp) {
            return;
        }

        if (empty($this->emspApiKey)) {
            $context->buildViolation('must have an api key')->atPath('emsp_api_key')->addViolation();
        }
        if (Entity::EMSP_MAILGUN === $this->emsp) {
            if (empty($this->emspApiUrl)) {
                $context->buildViolation('must have api url')->atPath('emsp_api_url')->addViolation();
            }
            if (empty($this->emspDomain)) {
                $context->buildViolation('must have domain')->atPath('emsp_domain')->addViolation();
            }
        }
    }
}
