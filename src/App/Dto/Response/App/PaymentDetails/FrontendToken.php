<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
