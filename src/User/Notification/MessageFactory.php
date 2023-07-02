<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 26.06.2026 ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\User\Notification;

use App\Entity\Customer;
use App\Repository\BrandSettingsRepositoryInterface;
use Parthenon\Common\Config;
use Parthenon\Notification\Email;
use Parthenon\User\Entity\InviteCode;
use Parthenon\User\Entity\UserInterface;
use Parthenon\User\Notification\MessageFactory as BaseMessageFactory;
use Parthenon\User\Notification\UserEmail;
use Twig\Environment;

class MessageFactory extends BaseMessageFactory
{
    public function __construct(Config $config, private Environment $twig, private BrandSettingsRepositoryInterface $brandSettingsRepository)
    {
        parent::__construct($config);
    }

    public function getInviteMessage(UserInterface $user, InviteCode $inviteCode): Email
    {
        $brand = $this->brandSettingsRepository->getByCode(Customer::DEFAULT_BRAND);
        $emailVariables = [
            'invite_url' => rtrim($this->config->getSiteUrl(), '/').'/signup/'.$inviteCode->getCode(),
            'brand_name' => $brand->getBrandName(),
        ];
        $content = $this->twig->render('Mail/invite.html.twig', $emailVariables);

        $message = UserEmail::createFromUser($user);
        $message->setSubject('Invited to use BillaBear for '.$brand->getBrandName())
            ->setContent($content)
            ->setToName('Invited User')
            ->setToAddress($inviteCode->getEmail());

        return $message;
    }
}
