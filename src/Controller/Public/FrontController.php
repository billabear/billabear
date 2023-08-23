<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\Public;

use App\Repository\SettingsRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FrontController
{
    #[Route('/portal/{vueRouting}', name: 'public_main', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    public function handlePublic(
        Environment $twig,
        SettingsRepositoryInterface $settingsRepository,
        #[Autowire(env: 'STRIPE_PRIVATE_API_KEY')] $privateApiKey,
    ) {
        if (empty($privateApiKey)) {
            return new RedirectResponse('/error/stripe');
        }

        try {
            $settings = $settingsRepository->getDefaultSettings();
        } catch (TableNotFoundException $exception) {
            return new RedirectResponse('/install');
        }

        return new Response($twig->render('public.html.twig'));
    }
}
