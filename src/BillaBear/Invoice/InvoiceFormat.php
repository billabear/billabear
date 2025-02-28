<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice;

enum InvoiceFormat: string
{
    case PDF = 'pdf';
    case ZUGFERD_V1 = 'zugferd_v1';
    case ZUGFERD_V2 = 'zugferd_v2';
}
