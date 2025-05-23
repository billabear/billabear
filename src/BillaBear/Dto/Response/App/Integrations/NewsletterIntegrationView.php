<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dto\Response\App\Integrations;

use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class NewsletterIntegrationView
{
    public function __construct(
        public array $integrations,
        public bool $enabled,
        #[SerializedName('integration_name')]
        public ?string $integrationName,
        public array $settings,
        public array $lists,
        #[SerializedName('marketing_list_id')]
        public ?string $marketingListId,
        #[SerializedName('announcement_list_id')]
        public ?string $announcementListId,
    ) {
    }
}
