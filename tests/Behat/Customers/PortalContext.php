<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Behat\Customers;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use BillaBear\Entity\ManageCustomerSession;
use BillaBear\Repository\Orm\CustomerRepository;
use BillaBear\Repository\Orm\ManageCustomerSessionRepository;
use BillaBear\Tests\Behat\SendRequestTrait;

class PortalContext implements Context
{
    use SendRequestTrait;
    use CustomerTrait;

    public function __construct(
        private Session $session,
        private CustomerRepository $customerRepository,
        private ManageCustomerSessionRepository $manageCustomerSessionRepository,
    ) {
    }

    #[When('I request the manage customer link for :email')]
    public function iRequestTheManageCustomerLinkFor($email): void
    {
        $customer = $this->getCustomerByEmail($email);

        $this->sendJsonRequest('GET', '/api/v1/customer/'.$customer->getId().'/manage-link');
    }

    #[Then('I will be given a token that is valid for managing the customer :email')]
    public function iWillBeGivenATokenThatIsValidForManagingTheCustomer($email): void
    {
        $data = $this->getJsonContent();
        $customer = $this->getCustomerByEmail($email);

        $session = $this->manageCustomerSessionRepository->findOneBy(['customer' => $customer, 'token' => $data['token']]);

        if (!$session instanceof ManageCustomerSession) {
            throw new \Exception('Did not find customer session');
        }
    }

    #[Given('there is a manage customer session for :email that expires in :time')]
    public function thereIsAManageCustomerSessionForThatExpiresIn($email, $time): void
    {
        $customer = $this->getCustomerByEmail($email);

        $manageCustomerSession = new ManageCustomerSession();
        $manageCustomerSession->setCustomer($customer);
        $manageCustomerSession->setToken(bin2hex(random_bytes(32)));
        $manageCustomerSession->setCreatedAt(new \DateTime());
        $manageCustomerSession->setUpdatedAt(new \DateTime());
        $manageCustomerSession->setExpiresAt(new \DateTime($time));

        $this->manageCustomerSessionRepository->getEntityManager()->persist($manageCustomerSession);
        $this->manageCustomerSessionRepository->getEntityManager()->flush();
    }

    #[When('I view the manage customer endpoint for :arg1')]
    public function iViewTheManageCustomerEndpointFor($email): void
    {
        $session = $this->getSession($email);

        $this->sendJsonRequest('GET', '/public/customer/'.$session->getToken().'/manage');
    }

    #[Then('I will see the customer portal information')]
    public function iWillSeeTheCustomerPortalInformation(): void
    {
        $data = $this->getJsonContent();

        if (!isset($data['customer'])) {
            throw new \Exception("Can't see customer portal");
        }
    }

    #[Then('I will not see the customer portal information')]
    public function iWillNotSeeTheCustomerPortalInformation(): void
    {
        $data = $this->getJsonContent();

        if (isset($data['customer'])) {
            throw new \Exception("Can't see customer portal");
        }
    }
}
