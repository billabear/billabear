<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Request\App\Invoice;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateInvoiceDelivery
{
    #[Assert\Type('string')]
    #[Assert\Choice(['email', 'sftp', 'webhook'])]
    private $type;

    #[Assert\Type('string')]
    #[Assert\Choice(['pdf', 'zugfred_v1'])]
    private $format;

    #[Assert\Type('string')]
    private $sftpHost;

    #[Assert\Type('string')]
    private $sftpUser;

    #[Assert\Type('string')]
    private $sftpPassword;

    #[Assert\Type('string')]
    private $sftpDir;

    #[Assert\Type('integer')]
    #[Assert\Positive]
    private $sftpPort;

    #[Assert\Type('string')]
    #[Assert\Url(protocols: ['http', 'https'])]
    private $webhookUrl;

    #[Assert\Type('string')]
    #[Assert\Choice(['POST', 'PUT'])]
    private $webhookMethod;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if ('email' === $this->type) {
            return;
        }

        if ('sftp' === $this->type) {
            if (!$this->sftpHost) {
                $context->buildViolation('Cannot be blank')
                    ->atPath('sftpHost')
                    ->addViolation();
            }
            if (!$this->sftpUser) {
                $context->buildViolation('Cannot be blank')
                    ->atPath('sftpUser')
                    ->addViolation();
            }
            if (!$this->sftpPassword) {
                $context->buildViolation('Cannot be blank')
                    ->atPath('sftpPassword')
                    ->addViolation();
            }
            if (!$this->sftpPort) {
                $context->buildViolation('Cannot be blank')
                    ->atPath('sftpPort')
                    ->addViolation();
            }
            if (!$this->sftpDir) {
                $context->buildViolation('Cannot be blank')
                    ->atPath('sftpDir')
                    ->addViolation();
            }

            return;
        }

        if ('webhook' === $this->type) {
            if (!$this->webhookUrl) {
                $context->buildViolation('Cannot be blank')
                    ->atPath('webhookUrl')
                    ->addViolation();
            }

            if (!$this->webhookMethod) {
                $context->buildViolation('Cannot be blank')
                    ->atPath('webhookMethod')
                    ->addViolation();
            }

            return;
        }
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getSftpHost()
    {
        return $this->sftpHost;
    }

    public function setSftpHost($sftpHost): void
    {
        $this->sftpHost = $sftpHost;
    }

    public function getSftpUser()
    {
        return $this->sftpUser;
    }

    public function setSftpUser($sftpUser): void
    {
        $this->sftpUser = $sftpUser;
    }

    public function getSftpPassword()
    {
        return $this->sftpPassword;
    }

    public function setSftpPassword($sftpPassword): void
    {
        $this->sftpPassword = $sftpPassword;
    }

    public function getSftpDir()
    {
        return $this->sftpDir;
    }

    public function setSftpDir($sftpDir): void
    {
        $this->sftpDir = $sftpDir;
    }

    public function getSftpPort()
    {
        return $this->sftpPort;
    }

    public function setSftpPort($sftpPort): void
    {
        $this->sftpPort = $sftpPort;
    }

    public function getWebhookUrl()
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl($webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getWebhookMethod()
    {
        return $this->webhookMethod;
    }

    public function setWebhookMethod($webhookMethod): void
    {
        $this->webhookMethod = $webhookMethod;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format): void
    {
        $this->format = $format;
    }
}
