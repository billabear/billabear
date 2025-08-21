<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\PaymentDetails;

use Symfony\Component\Serializer\Annotation\SerializedName;

class FrontendToken
{
    #[SerializedName('api_info')]
    private string $apiInfo;

    private string $token;

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getApiInfo(): string
    {
        return $this->apiInfo;
    }

    public function setApiInfo(string $apiInfo): void
    {
        $this->apiInfo = $apiInfo;
    }
}
