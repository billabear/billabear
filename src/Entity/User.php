<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\Payments\Subscriber\SubscriberInterface;
use Parthenon\User\Entity\MemberInterface;
use Parthenon\User\Entity\TeamInterface;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User extends \Parthenon\User\Entity\User
{
}
