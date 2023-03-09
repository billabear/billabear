<?php

namespace App\Tests\Behat;

use App\Entity\Team;

trait TeamTrait
{
    protected function getTeamByName(string $name): Team
    {
        $team = $this->teamRepository->findOneBy(['name' => $name]);

        if (!$team instanceof Team) {
            throw new \Exception("Can't find team");
        }

        $this->teamRepository->getEntityManager()->refresh($team);

        return $team;
    }
}
