<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
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
