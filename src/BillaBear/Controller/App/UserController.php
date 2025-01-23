<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Entity\User;
use BillaBear\User\UserProvider;
use Parthenon\User\Formatter\UserFormatterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController
{
    #[Route('/user', name: 'app_user')]
    public function userAction(UserProvider $userProvider, UserFormatterInterface $userFormatter): Response
    {
        /** @var User $user */
        $user = $userProvider->getUser();
        $output = [];
        $output['user'] = $userFormatter->format($user);

        return new JsonResponse($output);
    }
}
