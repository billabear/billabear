<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\User\Entity;

use Parthenon\User\Entity\ForgotPasswordCode;
use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\TeamInterface;
use Parthenon\User\Entity\TeamInviteCode;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Factory\EntityFactory as BaseFactory;

class EntityFactory extends BaseFactory
{
    public function buildPasswordReset(UserInterface $user): ForgotPasswordCode
    {
        return \BillaBear\Entity\ForgotPasswordCode::createForUser($user);
    }

    public function buildInviteCode(UserInterface $user, string $email, ?string $role = null): InviteCode
    {
        return \BillaBear\Entity\InviteCode::createForUser($user, $email, $role);
    }

    public function buildTeamInviteCode(UserInterface $user, TeamInterface $team, string $email, string $role): TeamInviteCode
    {
        return \BillaBear\Entity\TeamInviteCode::createForUserAndTeam($user, $team, $email, $role);
    }
}
