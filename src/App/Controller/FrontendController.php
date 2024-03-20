<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller;

use App\Repository\SettingsRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FrontendController
{
    #[Route('/', name: 'app_index_landing', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/login', name: 'app_public', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/signup', name: 'app_signup', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/signup/{code}', name: 'app_invite', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/forgot-password', name: 'app_forgot_password', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/forgot-password/{code}', name: 'app_forgot_password_confirm', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/confirm-email/{code}', name: 'app_confirm_email', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/site/{vueRouting}', name: 'app_main', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/app/plan', name: 'app_plan', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    public function home(
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

        return new Response($twig->render('index.html.twig'));
    }

    #[Route('/error/stripe', name: 'app_site_error', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/error/stripe-invalid', name: 'app_site_error_invalid', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    public function stripeError(
        Environment $twig, )
    {
        return new Response($twig->render('index.html.twig'));
    }
}
