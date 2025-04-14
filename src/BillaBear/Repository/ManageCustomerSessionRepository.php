<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\ManageCustomerSession;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class ManageCustomerSessionRepository extends DoctrineRepository implements ManageCustomerSessionRepositoryInterface
{
    public function getByToken(string $token): ManageCustomerSession
    {
        $session = $this->entityRepository->findOneBy(['token' => $token]);

        if (!$session instanceof ManageCustomerSession) {
            throw new NoEntityFoundException(sprintf("Unable to find manage customer session for token '%s'", $token));
        }

        return $session;
    }
}
