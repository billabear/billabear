<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Dto\Response\App\PaymentDetails;

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
