<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers\Integrations;

use BillaBear\Dto\Generic\App\Integrations\NewsletterList as AppDto;
use BillaBear\Integrations\Newsletter\NewsletterList;

class NewsletterListsDataMapper
{
    public function createAppDto(NewsletterList $list): AppDto
    {
        return new AppDto(
            $list->id,
            $list->name,
        );
    }
}
