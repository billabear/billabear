<?php

namespace App\Repository;

use Parthenon\Payments\Repository\SubscriberRepositoryInterface;

interface TeamRepositoryInterface extends \Parthenon\User\Repository\TeamRepositoryInterface, SubscriberRepositoryInterface
{
}
