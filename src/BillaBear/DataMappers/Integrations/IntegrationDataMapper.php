<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Integrations;

use BillaBear\Dto\Generic\App\Integrations\Integration as AppDto;
use BillaBear\Integrations\IntegrationInterface;

class IntegrationDataMapper
{
    public function createAppDto(IntegrationInterface $integration): AppDto
    {
        return new AppDto(
            $integration->getName(),
            $integration->getAuthenticationType()->value,
            $integration->getSettings(),
        );
    }
}
