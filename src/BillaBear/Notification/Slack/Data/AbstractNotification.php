<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Notification\Slack\Data;

use BillaBear\Entity\SlackNotification;
use BillaBear\Notification\Slack\SlackNotificationEvent;
use Parthenon\Notification\Slack\MessageBuilder;

abstract class AbstractNotification implements SlackNotificationInterface
{
    abstract public function getEvent(): SlackNotificationEvent;

    final public function getMessage(SlackNotification $slackNotification): array
    {
        $template = $slackNotification->getMessageTemplate();

        $data = $this->getData();

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                if ($value instanceof \DateTime) {
                    $value = $value->format(DATE_ATOM);
                }

                $template = preg_replace("|({{\W*".$key."\W*}})|isU", $value, $template, -1);
                continue;
            }

            foreach ($value as $subKey => $subValue) {
                if ($subValue instanceof \DateTime) {
                    $subValue = $subValue->format(DATE_ATOM);
                }

                $template = preg_replace("|({{\W*".$key."\.".$subKey."\W*}})|isU", $subValue, $template, -1);
            }
        }

        $messageBuilder = new MessageBuilder();
        $messageBuilder->addTextSection($template)->closeSection();

        return $messageBuilder->build();
    }

    abstract protected function getData(): array;
}
