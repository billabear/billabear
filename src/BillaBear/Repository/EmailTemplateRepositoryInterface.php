<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\EmailTemplate;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface EmailTemplateRepositoryInterface extends CrudRepositoryInterface
{
    public function getByNameAndLocale(string $name, string $locale): EmailTemplate;

    public function getByNameAndLocaleAndBrand(string $name, string $locale, string $brand): ?EmailTemplate;
}
