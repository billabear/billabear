<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Subscriptions;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Behat\Step\When;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\ManageCustomerSessionRepository;
use BillaBear\Repository\Orm\SubscriptionPlanRepository;
use BillaBear\Repository\Orm\SubscriptionRepository;
use BillaBear\Tests\Behat\Customers\CustomerTrait;
use BillaBear\Tests\Behat\SendRequestTrait;

class PortalContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;
    use SubscriptionTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private ManageCustomerSessionRepository $manageCustomerSessionRepository,
        private SubscriptionRepository $subscriptionRepository,
        private SubscriptionPlanRepository $planRepository,
    ) {
    }

    #[When('I cancel via the portal the subscription :planName for :customerEmail')]
    public function iCancelViaThePortalTheSubscriptionFor($planName, $customerEmail): void
    {
        $session = $this->getSession($customerEmail);
        $subscription = $this->getSubscription($customerEmail, $planName);

        $this->sendJsonRequest('POST', sprintf('/public/subscription/%s/%s/cancel', $session->getToken(), $subscription->getId()));
    }
}
