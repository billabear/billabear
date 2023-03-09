<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="invite_codes")
 */
class InviteCode extends \Parthenon\User\Entity\InviteCode
{
}
