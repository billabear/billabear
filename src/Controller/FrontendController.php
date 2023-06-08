<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller;

use App\Repository\SettingsRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FrontendController
{
    #[Route('/', name: 'app_index_landing', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/login', name: 'app_public', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/signup', name: 'app_signup', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/forgot-password', name: 'app_forgot_password', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/forgot-password/{code}', name: 'app_forgot_password_confirm', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/confirm-email/{code}', name: 'app_confirm_email', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/site/{vueRouting}', name: 'app_main', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/app/plan', name: 'app_plan', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    public function home(
        Environment $twig,
        SettingsRepositoryInterface $settingsRepository)
    {
        try {
            $settings = $settingsRepository->getDefaultSettings();
        } catch (TableNotFoundException $exception) {
            return new RedirectResponse('/install');
        }

        return new Response($twig->render('index.html.twig'));
    }
}
