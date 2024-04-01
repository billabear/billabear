<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Tests\Behat\Settings;

use App\Entity\User;
use App\Repository\Orm\UserRepository;
use App\Tests\Behat\SendRequestTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class UserManagementContext implements Context
{
    use SendRequestTrait;

    public function __construct(
        private Session $session,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @When I view the user management page for :arg1
     */
    public function iViewTheUserManagementPageFor($email)
    {
        $user = $this->getUser($email);
        $this->sendJsonRequest('GET', '/app/settings/user/'.(string) $user->getId());
    }

    /**
     * @Then I will see the user has the role :arg1
     */
    public function iWillSeeTheUserHasTheRole($arg1)
    {
        $data = $this->getJsonContent();

        if (!in_array($arg1, $data['user']['roles'])) {
            throw new \Exception('Doesnt have the role');
        }
    }

    /**
     * @When I update the user management page for :arg1 with:
     */
    public function iUpdateTheUserManagementPageForWith($email, TableNode $table)
    {
        $data = $table->getRowsHash();
        $user = $this->getUser($email);
        $payload = [
            'email' => $data['Email'] ?? null,
            'roles' => explode(',', $data['Roles'] ?? ''),
        ];
        $this->sendJsonRequest('POST', '/app/settings/user/'.(string) $user->getId(), $payload);
    }

    /**
     * @Then there will be a user with the email :arg1
     */
    public function thereWillBeAUserWithTheEmail($email)
    {
        $this->getUser($email);
    }

    /**
     * @Then the user with the email :arg1 has the role :arg2
     */
    public function theUserWithTheEmailHasTheRole($email, $role)
    {
        $user = $this->getUser($email);

        if (!in_array($role, $user->getRoles())) {
            throw new \Exception('Doesnt have the role');
        }
    }

    protected function getUser($email)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            throw new \Exception('No user found');
        }
        $this->userRepository->getEntityManager()->refresh($user);

        return $user;
    }
}
