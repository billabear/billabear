<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Generic\App;

use Symfony\Component\Serializer\Attribute\SerializedName;

class InvoiceDeliverySettings
{
    private string $id;

    private string $type;

    private string $format;

    #[SerializedName('sftp_host')]
    private ?string $sftpHost;

    #[SerializedName('sftp_user')]
    private ?string $sftpUser;

    #[SerializedName('sftp_password')]
    private ?string $sftpPassword;

    #[SerializedName('sftp_dir')]
    private ?string $sftpDir;

    #[SerializedName('sftp_port')]
    private ?int $sftpPort;

    #[SerializedName('webhook_url')]
    private ?string $webhookUrl;

    #[SerializedName('webhook_method')]
    private ?string $webhookMethod;

    private ?string $email;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getSftpHost(): ?string
    {
        return $this->sftpHost;
    }

    public function setSftpHost(?string $sftpHost): void
    {
        $this->sftpHost = $sftpHost;
    }

    public function getSftpUser(): ?string
    {
        return $this->sftpUser;
    }

    public function setSftpUser(?string $sftpUser): void
    {
        $this->sftpUser = $sftpUser;
    }

    public function getSftpPassword(): ?string
    {
        return $this->sftpPassword;
    }

    public function setSftpPassword(?string $sftpPassword): void
    {
        $this->sftpPassword = $sftpPassword;
    }

    public function getSftpDir(): ?string
    {
        return $this->sftpDir;
    }

    public function setSftpDir(?string $sftpDir): void
    {
        $this->sftpDir = $sftpDir;
    }

    public function getSftpPort(): ?int
    {
        return $this->sftpPort;
    }

    public function setSftpPort(?int $sftpPort): void
    {
        $this->sftpPort = $sftpPort;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(?string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getWebhookMethod(): ?string
    {
        return $this->webhookMethod;
    }

    public function setWebhookMethod(?string $webhookMethod): void
    {
        $this->webhookMethod = $webhookMethod;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
