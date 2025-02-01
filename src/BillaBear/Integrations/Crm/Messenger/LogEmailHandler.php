<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Integrations\Crm\Messenger;

use BillaBear\Integrations\Crm\Action\LogEmail as Action;
use BillaBear\Repository\CustomerRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LogEmailHandler
{
    use LoggerAwareTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private Action $action,
    ) {
    }

    public function __invoke(LogEmail $message)
    {
        $this->getLogger()->info('Logging email for customer', ['customer_id' => $message->customerId, 'template_name' => $message->templateName]);
        $customer = $this->customerRepository->findById($message->customerId);
        $this->action->logCustomer($customer, $message->templateName);
    }
}
