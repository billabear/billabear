<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations;

enum IntegrationType: string
{
    case ACCOUNTING = 'accounting';
    case NOTIFICATION = 'notification';
    case CUSTOMER_SUPPORT = 'customer_support';
    case NEWSLETTER = 'newsletter';
    case CRM = 'crm';
}
