<?php

namespace App\Tests\Behat;

use App\Entity\ForgotPasswordCode;
use App\Entity\InviteCode;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\Orm\InviteCodeRepository;
use App\Repository\Orm\ForgotPasswordCodeRepository;
use App\Repository\Orm\TeamRepository;
use App\Repository\Orm\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Doctrine\ORM\EntityManagerInterface;
use Parthenon\Athena\Entity\Link;
use Parthenon\Athena\Entity\Notification;
use Parthenon\Payments\Entity\Subscription;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserContext implements Context
{
    use SendRequestTrait;
    use TeamTrait;

    private array $formFields = [];

    private array $response = [];

    private int $count;

    private string $passwordHash;

    public function __construct(
        private Session                        $session,
        private UserRepository                 $repository,
        private EntityManagerInterface         $entityManager,
        private PasswordHasherFactoryInterface $hasherFactory,
        private ForgotPasswordCodeRepository   $passwordResetRepository,
        private InviteCodeRepository           $inviteCodeRepository,
        private TeamRepository                 $teamRepository
    ) {
    }

    /**
     * @When I try to sign up
     */
    public function iTryToSignUp()
    {
        $this->count = $this->repository->count([]);
        $this->sendJsonRequest('POST', '/api/user/signup', $this->formFields);
    }

    /**
     * @Then I will see an error about an invalid email address
     */
    public function iWillSeeAnErrorAboutAnInvalidEmailAddress()
    {
        $json = json_decode($this->session->getPage()->getContent(), true);

        if (!$json || $json['errors']['email'] !== ['A valid email must be provided.']) {
            throw new \Exception("Can't find an error about an invalid email");
        }
    }

    /**
     * @Given I have given the field :fieldName the value :fieldValue
     */
    public function iGivenTheFieldTheValue($fieldName, $fieldValue)
    {
        $this->formFields[$fieldName] = $fieldValue;
    }

    /**
     * @Then I will see an error about not having a password
     */
    public function iWillSeeAnErrorAboutNotHavingAPassword()
    {
        $json = json_decode($this->session->getPage()->getContent(), true);

        if (!$json || $json['errors']['password'] !== ['The password must be provided.']) {
            throw new \Exception("Can't find an error about a missing password");
        }
    }

    /**
     * @Then there will not be a new user registered
     */
    public function thereWillNotBeANewUserRegistered()
    {
        if ($this->count != $this->repository->count([])) {
            throw new \Exception('The user count is not the same as before.');
        }
    }

    /**
     * @Then there will be a new user registered
     */
    public function thereWillBeANewUserRegistered()
    {
        if ($this->count >= $this->repository->count([])) {
            throw new \Exception('The user count has not increased.');
        }
    }

    /**
     * @When I login as :username with the password :password
     * @Given I have logged in as :username with the password :password
     */
    public function iLoginAsWithThePassword($username, $password)
    {
        $this->sendJsonRequest('POST', '/api/authenticate', ['username' => $username, 'password' => $password]);
    }

    /**
     * @Then I will see a login error
     */
    public function iWillSeeALoginError()
    {
        $content = $this->session->getPage()->getContent();
        $json = json_decode($content, true);
        if (is_null($json) || !isset($json['error'])) {
            throw new \Exception("Didn't see an login error");
        }
    }

    /**
     * @Given a confirmed user :arg1 with the password :arg2 exists
     * @Given a confirmed user :arg1 with the password :arg2 and the confirmation code :arg3 exists
     */
    public function aConfirmedUserWithThePasswordExists($username, $password, $confirmationCode = 'confirmation')
    {
        $this->createUser($username, $password, $confirmationCode, true);
    }

    /**
     * @Given an unconfirmed user :arg1 with the password :arg2 exists
     * @Given an unconfirmed user :arg1 with the password :arg2 and the confirmation code :arg3 exists
     */
    public function anUnconfirmedUserWithThePasswordExists($username, $password, $confirmationCode = 'confirmation')
    {
        $this->createUser($username, $password, $confirmationCode, false);
    }

    protected function createUser($username, $password, $confirmationCode, $confirmed, $name = 'A test user', $isAdmin = false, $bulk = false)
    {
        $user = new User();
        if (!$bulk) {
            $encodedPassword = $this->hasherFactory->getPasswordHasher($user)->hash($password);
        } else {
            $encodedPassword = $password;
        }
        $user->setEmail($username);
        $user->setPassword($encodedPassword);
        $user->setName($name);
        $user->setConfirmationCode($confirmationCode);
        $user->setIsConfirmed($confirmed);
        $user->setCreatedAt(new \DateTime('now'));
        $user->setRoles($isAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);

        $this->repository->getEntityManager()->persist($user);
        if (!$bulk) {
            $this->repository->getEntityManager()->flush();
        }

        return $user;
    }

    /**
     * @Then I will be logged in
     */
    public function iWillBeLoggedIn()
    {
        if ($this->session->getPage()->hasContent('Login error')) {
            throw new \Exception("Didn't see an login error");
        }
    }

    /**
     * @When I confirm the code :arg1
     */
    public function iConfirmTheCode($code)
    {
        $this->sendJsonRequest('GET', '/api/user/confirm/'.$code);
    }

    /**
     * @Then the user :arg1 will be confirmed
     */
    public function theUserWillBeConfirmed($arg1)
    {
        $user = $this->repository->findOneBy(['email' => $arg1]);
        $this->repository->getEntityManager()->refresh($user);
        if (!$user->isConfirmed()) {
            throw new \Exception('User is not confirmed when it should be');
        }
    }

    /**
     * @Then the user :arg1 will not be confirmed
     */
    public function theUserWillNotBeConfirmed($arg1)
    {
        $user = $this->repository->findOneBy(['email' => $arg1]);
        $this->repository->getEntityManager()->refresh($user);
        if ($user->isConfirmed()) {
            throw new \Exception('User is confirmed when it should not be');
        }
    }

    /**
     * @When I request to reset my password for :arg1
     */
    public function iRequestToResetMyPasswordFor($username)
    {
        $this->count = $this->passwordResetRepository->count([]);
        $this->sendJsonRequest('POST', '/api/user/reset', ['email' => $username]);
    }

    /**
     * @Then there will be a new password reset code for :arg1
     */
    public function thereWillBeANewPasswordResetCodeFor($arg1)
    {
        if ($this->count >= $this->passwordResetRepository->count([])) {
            throw new \Exception('No new reset code');
        }
    }

    /**
     * @Given a password reset code :arg1 for :arg2 exists
     */
    public function aPasswordResetCodeExists($code, $email)
    {
        $user = $this->fetchUser($email);
        $passwordReset = ForgotPasswordCode::createForUser($user);
        $passwordReset->setCode($code);
        $this->passwordResetRepository->getEntityManager()->persist($passwordReset);
        $this->passwordResetRepository->getEntityManager()->flush();
    }

    /**
     * @When I reset my password to :arg1 with the code :arg2 for :arg3
     */
    public function iResetMyPasswordToWithTheCode($password, $code, $email)
    {
        $user = $this->repository->findOneBy(['email' => $email]);
        $this->passwordHash = $user->getPassword();

        $this->sendJsonRequest('POST', '/api/user/reset/'.$code, ['password' => $password]);

        $this->count = $this->passwordResetRepository->count([]);
    }

    /**
     * @Then there will be a new password for :arg1
     */
    public function thereWillBeANewPasswordFor($email)
    {
        $user = $this->repository->findOneBy(['email' => $email]);
        $this->repository->getEntityManager()->refresh($user);
        if ($this->passwordHash === $user->getPassword()) {
            throw new \Exception('The password hash has not been changed');
        }
    }

    /**
     * @Then there will not be a new password for :arg1
     */
    public function thereWillNotBeANewPasswordFor($email)
    {
        $user = $this->fetchUser($email);
        if ($this->passwordHash !== $user->getPassword()) {
            throw new \Exception('The password hash has been changed');
        }
    }

    /**
     * @Given the password reset code :arg1 has expired
     */
    public function thePasswordResetCodeHasExpired($code)
    {
        /** @var ForgotPasswordCode $passwordReset */
        $passwordReset = $this->passwordResetRepository->findOneBy(['code' => $code]);
        $this->repository->getEntityManager()->refresh($passwordReset);
        $passwordReset->setExpiresAt(new \DateTime('-1 hour'));

        $this->passwordResetRepository->getEntityManager()->persist($passwordReset);
        $this->passwordResetRepository->getEntityManager()->flush($passwordReset);
    }

    /**
     * @Given the password reset code :arg1 has already been used
     */
    public function thePasswordResetCodeHasAlreadyBeenUsed($code)
    {
        /** @var ForgotPasswordCode $passwordReset */
        $passwordReset = $this->passwordResetRepository->findOneBy(['code' => $code]);
        $this->repository->getEntityManager()->refresh($passwordReset);
        $passwordReset->setUsed(true);

        $this->passwordResetRepository->getEntityManager()->persist($passwordReset);
        $this->passwordResetRepository->getEntityManager()->flush($passwordReset);
    }

    /**
     * @Given I have logged as :arg1
     */
    public function iHaveLoggedAs($arg1)
    {
        $this->iLoginAsWithThePassword($arg1, 'RealPassword');
    }



    /**
     * @When I edit my settings with the name :arg1
     */
    public function iEditMyProfileWithTheName($arg1)
    {
        $this->sendJsonRequest('GET', '/api/user/settings');
        $content = $this->getJsonContent()['form'];
        $output = [];
        foreach ($content as $key => $value) {
            $output[$key] = $value;
        }

        $output['name'] = $arg1;
        $this->sendJsonRequest('POST', '/api/user/settings', $output);
    }

    /**
     * @Then the user :arg1 will have the name :arg2
     */
    public function theUserWillHaveTheName($arg1, $arg2)
    {
        $user = $this->fetchUser($arg1);

        if ($user->getName() != $arg2) {
            throw new \Exception("The name doesn't match :".$user->getName());
        }
    }

    /**
     * @Given I am not logged in
     */
    public function iAmNotLoggedIn()
    {
    }

    /**
     * @When I visit the settings page
     */
    public function iVisitTheProfiilePage()
    {
        $this->session->visit('/api/user/settings');
    }

    /**
     * @Then I will be on the login page
     */
    public function iWillBeOnTheLoginPage()
    {
        if (401 !== $this->session->getStatusCode()) {
            throw new \Exception('Was not given an unauthorized response');
        }
    }

    /**
     * @param $email
     *
     * @return User|null
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function fetchUser($email)
    {
        /** @var User $user */
        $user = $this->repository->findOneBy(['email' => $email]);
        $this->repository->getEntityManager()->refresh($user);

        return $user;
    }

    /**
     * @When I change my password to :arg1 giving my current password as :arg2
     */
    public function iChangeMyPasswordToGivingMyCurrentPasswordAs($newPassword, $currentPassword)
    {
        $this->sendJsonRequest('POST', '/api/user/password', ['password' => $currentPassword, 'new_password' => $newPassword]);
    }

    /**
     * @When I visit the change password page
     */
    public function iVisitTheChangePasswordPage()
    {
    }

    /**
     * @Then the password :arg1 will not be valid for :arg2
     */
    public function thePasswordWillNotBeValidFor($arg1, $arg2)
    {
        $user = $this->fetchUser($arg2);

        $passwordHasher = $this->hasherFactory->getPasswordHasher($user);

        if ($passwordHasher->verify($user->getPassword(), $arg1)) {
            throw new \Exception('Password is valid');
        }
    }

    /**
     * @Then the password :arg1 will be valid for :arg2
     */
    public function thePasswordWillBeValidFor($arg1, $arg2)
    {
        $user = $this->fetchUser($arg2);

        $passwordHasher = $this->hasherFactory->getPasswordHasher($user);

        if (!$passwordHasher->verify($user->getPassword(), $arg1)) {
            throw new \Exception('Password is not valid');
        }
    }

    /**
     * @Given that the following users exist:
     */
    public function thatTheFollowingUsersExist(TableNode $table)
    {
        $users = $table->getColumnsHash();

        foreach ($users as $user) {
            $this->createUser($user['Email'], $user['Password'], 'ddd', true, $user['Name'], false, true);
        }

        $this->repository->getEntityManager()->flush();
    }

    /**
     * @Given an admin user :arg1 with the password :arg2 exist
     */
    public function anAdminUserWithThePasswordExist($email, $password)
    {
        $this->createUser($email, $password, 'fake', true, 'A system user', true);
    }

    /**
     * @When I view the user list page with the following settings:
     */
    public function iViewTheUserListPageWithTheFollowingSettings(TableNode $table)
    {
        $info = $table->getRowsHash();

        $queryString = '?';
        foreach ($info as $key => $value) {
            $queryString .= urlencode($key).'='.urlencode($value).'&';
        }

        $this->session->visit('/athena/user/list'.$queryString);
    }

    /**
     * @When I click next
     */
    public function iClickNext()
    {
        try {
            $this->session->getPage()->clickLink('crud_list_next');
        } catch (\Throwable $e) {
            echo $this->session->getPage()->getContent();
            throw $e;
        }
    }

    /**
     * @Then I should see :arg1 items
     */
    public function iShouldSeeItems($arg1)
    {
        $items = $this->session->getPage()->findAll('css', '.table-item');
        $count = count($items);
        if ($count != $arg1) {
            throw new \Exception('Found '.$count.' items');
        }
    }

    /**
     * @Then I should see :arg1
     */
    public function iShouldSee($arg1)
    {
        if (!$this->session->getPage()->hasContent($arg1)) {
            echo $this->session->getPage()->getContent();
            throw new \Exception("Can't see ".$arg1);
        }
    }

    /**
     * @Then I should not see :arg1
     */
    public function iShouldNotSee($arg1)
    {
        if ($this->session->getPage()->hasContent($arg1)) {
            throw new \Exception('Can see '.$arg1);
        }
    }

    /**
     * @When I view the user information for the user :arg1
     */
    public function iViewTheUserInformationForTheUser($arg1)
    {
        $user = $this->fetchUser($arg1);

        $this->session->visit('/athena/user/'.$user->getId().'/read');
    }

    /**
     * @When I delete the user information for the user :arg1
     */
    public function iDeleteTheUserInformationForTheUser($arg1)
    {
        $user = $this->fetchUser($arg1);

        $this->session->visit('/athena/user/'.$user->getId().'/delete');
    }

    /**
     * @Then the user :arg1 should be deleted
     */
    public function theUserShouldBeDeleted($arg1)
    {
        $user = $this->fetchUser($arg1);
        if (!$user->isDeleted()) {
            throw new \Exception('The user is not deleted');
        }
    }

    /**
     * @When I undelete the user information for the user :arg1
     */
    public function iUndeleteTheUserInformationForTheUser($email)
    {
        $this->theUserIsMarkedAsDeleted($email);
        $user = $this->fetchUser($email);

        $this->session->visit('/athena/user/'.$user->getId().'/undelete');
    }

    /**
     * @Given the user :arg1 is marked as deleted
     */
    public function theUserIsMarkedAsDeleted($email)
    {
        $user = $this->fetchUser($email);
        $user->markAsDeleted();
        $this->repository->getEntityManager()->persist($user);
        $this->repository->getEntityManager()->flush();
    }

    /**
     * @Then the user :arg1 should not be deleted
     */
    public function theUserShouldNotBeDeleted($arg1)
    {
        $user = $this->fetchUser($arg1);
        if ($user->isDeleted()) {
            throw new \Exception('The user is deleted');
        }
    }

    /**
     * @When I edit the user :arg1 with the data:
     */
    public function iEditTheUserWithTheData($arg1, TableNode $table)
    {
        $user = $this->fetchUser($arg1);

        $this->session->visit('/athena/user/'.$user->getId().'/edit');

        foreach ($table->getRowsHash() as $fieldName => $value) {
            $this->session->getPage()->fillField('form['.$fieldName.']', $value);
        }
        $this->session->getPage()->pressButton('crud_edit_submit');
    }

    /**
     * @Then the name for :arg1 should be :arg2
     */
    public function theNameForShouldBe($arg1, $arg2)
    {
        $user = $this->fetchUser($arg1);

        if ($arg2 !== $user->getName()) {
            throw new \Exception("Name doesn't match");
        }
    }

    /**
     * @When I invite :arg1
     */
    public function iInvite($email)
    {
        $this->sendJsonRequest('POST', '/api/user/invite', ['email' => $email]);
    }

    /**
     * @Then there will be an invite code for :arg1
     */
    public function thereWillBeAnInviteCodeFor($email)
    {
        $inviteCode = $this->inviteCodeRepository->findOneBy(['email' => $email]);

        if (!$inviteCode) {
            throw new \Exception('No invite code found');
        }
    }

    /**
     * @Given the invite code :arg1 exists
     */
    public function theInviteCode($code)
    {
        $user = $this->createUser('inviteUser', 'fddfssdf', 'ddd', true);

        $inviteCode = InviteCode::createForUser($user, 'random.email@example.org');
        $inviteCode->setCode($code);
        $this->inviteCodeRepository->getEntityManager()->persist($inviteCode);
        $this->inviteCodeRepository->getEntityManager()->flush();
    }

    /**
     * @When I try to sign up with the code :arg1
     */
    public function iTryToSignUpWithTheCode($code)
    {
        $this->count = $this->repository->count([]);

        $this->count = $this->repository->count([]);
        $this->sendJsonRequest('POST', '/api/user/signup/'.$code, $this->formFields);
    }

    /**
     * @Then the invite code :arg1 will have been used by :arg2
     */
    public function theInviteCodeWillHaveBeenUsedBy($code, $email)
    {
        /** @var InviteCode $inviteCode */
        $inviteCode = $this->inviteCodeRepository->findOneBy(['code' => $code]);
        $this->inviteCodeRepository->getEntityManager()->refresh($inviteCode);

        if (!$inviteCode->isUsed()) {
            throw new \Exception('Invite code is not used');
        }

        if ($inviteCode->getInvitedUser()->getEmail() !== $email) {
            throw new \Exception('Invite used by someone else');
        }
    }

    /**
     * @When I view the rule engine list page
     */
    public function iViewTheRuleEngineListPage()
    {
        $this->session->visit('/athena/rule-engine');
    }

    /**
     * @Given the following notifications exist:
     */
    public function theFollowingNotificationsExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $notification = new Notification();
            $notification->setMessageTemplate($row['Message']);
            $notification->setCreatedAt(new \DateTime('now'));
            $notification->setLink(new Link($row['Url Path'], \json_decode($row['Url Details'], true)));

            if (isset($row['Read']) && 'true' == strtolower($row['Read'])) {
                $notification->markAsRead();
            }
            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();
    }

    /**
     * @Given the following accounts exist:
     */
    public function theFollowingAccountsExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $user = $this->createUser($row['Email'], $row['Password'], 'fake', ($row['Confirmed'] ?? null) !== 'False', $row['Name']);
            try {
                $team = $this->getTeamByName($row['Team']);
                $team->addMember($user);
                $user->setTeam($team);
                $this->teamRepository->getEntityManager()->persist($user);
                $this->teamRepository->getEntityManager()->flush();
            } catch (\Throwable $entityFoundException) {
                $this->createTeam($user, $row['Team']);
            }
        }
    }

    /**
     * @Given the following teams exist:
     */
    public function theFollowingTeamsExist(TableNode $table)
    {
        foreach ($table->getColumnsHash() as $row) {
            $this->createTeam(null, $row['Name'], $row['Plan'] ?? 'Trial');
        }
    }

    protected function createTeam(?User $user, $name, $plan = 'Trial')
    {
        $team = new Team();
        $team->setName($name);
        if ($user) {
            $team->addMember($user);
        }
        $team->setCreatedAt(new \DateTime('now'));
        $team->setSubscription(new Subscription());
        $team->getSubscription()->setPlanName($plan);
        $team->getSubscription()->setValidUntil(new \DateTime('+7 days'));
        $team->getSubscription()->setActive(true);

        $this->teamRepository->getEntityManager()->persist($team);

        $this->teamRepository->getEntityManager()->flush();
        if ($user) {
            $user->setTeam($team);
            $this->teamRepository->getEntityManager()->persist($user);
        }

        $this->teamRepository->getEntityManager()->flush();
    }


    /**
     * @When I sent an invite to :arg1
     */
    public function iSentAnInviteTo($email)
    {
        $this->sendJsonRequest('POST', '/api/user/team/invite', ['email' => $email]);
    }

    /**
     * @Then I should not see :arg1 as an invited user
     */
    public function iShouldNotSeeAsAnInvitedUser($email)
    {
        $jsonData = json_decode($this->session->getPage()->getContent(), true);

        if (!$jsonData) {
            echo $this->session->getPage()->getContent();
        }

        foreach ($jsonData['sent_invites'] as $invite) {
            if ($invite['email'] == $email) {
                throw new \Exception('Email found');
            }
        }
    }
}
