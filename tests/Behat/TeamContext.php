<?php

namespace App\Tests\Behat;

use App\Repository\Orm\InviteCodeRepository;
use App\Repository\Orm\TeamInviteCodeRepository;
use App\Repository\Orm\TeamRepository;
use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Parthenon\User\Entity\InviteCode;

class TeamContext implements Context
{
    use SendRequestTrait;
    use TeamTrait;

    public function __construct(
        private Session $session,
        private TeamRepository $teamRepository,
        private TeamInviteCodeRepository $inviteCodeRepository
    ) {

    }

    /**
     * @When I view the team view
     */
    public function iViewTheTeamView()
    {
        $this->sendJsonRequest('GET', '/api/user/team');
    }

    /**
     * @Then there should be an invite for :arg1 to :arg2
     */
    public function thereShouldBeAnInviteForTo($email, $teamName)
    {
        $team = $this->getTeamByName($teamName);
        $inviteCode = $this->inviteCodeRepository->findOneBy(['email' => $email, 'team' => $team]);

        if (!$inviteCode instanceof InviteCode) {
            throw new \Exception('No invite code');
        }
    }

    /**
     * @Then I should be told that the email has already been invited
     */
    public function iShouldBeToldThatTheEmailHasAlreadyBeenInvited()
    {
        $jsonData = json_decode($this->session->getPage()->getContent(), true);

        if (!$jsonData['already_invited']) {
            throw new \Exception('Not declared as already invited');
        }
    }

    /**
     * @When I cancel the invite for :arg1
     */
    public function iCancelTheInviteFor($email)
    {
        /** @var InviteCode $inviteCode */
        $inviteCode = $this->inviteCodeRepository->findOneBy(['email' => $email]);

        $this->sendJsonRequest('POST', '/api/user/team/invite/'.$inviteCode->getId().'/cancel');
    }

    /**
     * @Then I should see :arg1 as an invited user
     */
    public function iShouldSeeAsAnInvitedUser($email)
    {
        $jsonData = json_decode($this->session->getPage()->getContent(), true);

        foreach ($jsonData['sent_invites'] as $invite) {
            if ($invite['email'] == $email) {
                return;
            }
        }

        throw new \Exception('The user is not in the invited list');
    }

    /**
     * @Then I should see the member :arg1 in the member list
     */
    public function iShouldSeeTheMemberInTheMemberList($email)
    {
        $jsonData = json_decode($this->session->getPage()->getContent(), true);

        foreach ($jsonData['members'] as $invite) {
            if ($invite['email'] == $email) {
                return;
            }
        }

        throw new \Exception('Email not found');
    }

    /**
     * @Then the invite for :arg1 shouldn't be usable
     */
    public function theInviteForShouldntBeUsable($email)
    {
        /** @var InviteCode $inviteCode */
        $inviteCode = $this->inviteCodeRepository->findOneBy(['email' => $email]);
        $this->inviteCodeRepository->getEntityManager()->refresh($inviteCode);

        if (!$inviteCode->isUsed()) {
            throw new \Exception('Invite is usable');
        }
    }
}