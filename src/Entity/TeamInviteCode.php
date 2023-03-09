<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="team_invite_codes")
 */
class TeamInviteCode extends \Parthenon\User\Entity\TeamInviteCode
{
}
