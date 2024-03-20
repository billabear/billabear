<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Repository;

use App\Entity\EmailTemplate;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

interface EmailTemplateRepositoryInterface extends CrudRepositoryInterface
{
    public function getByNameAndLocale(string $name, string $locale): EmailTemplate;

    public function getByNameAndLocaleAndBrand(string $name, string $locale, string $brand): ?EmailTemplate;
}
