<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\SlackNotification;
use BillaBear\Notification\Slack\SlackNotificationEvent;

interface SlackNotificationInterface
{
    public function getEvent(): SlackNotificationEvent;

    public function getMessage(SlackNotification $slackNotification): array;
}
