<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller;

use BillaBear\Repository\SettingsRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class FrontendController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/', name: 'app_index_landing')]
    #[Route('/login', name: 'app_public')]
    #[Route('/signup', name: 'app_signup')]
    #[Route('/signup/{code}', name: 'app_invite')]
    #[Route('/forgot-password', name: 'app_forgot_password')]
    #[Route('/forgot-password/{code}', name: 'app_forgot_password_confirm')]
    #[Route('/confirm-email/{code}', name: 'app_confirm_email')]
    #[Route('/site/{vueRouting}', name: 'app_main', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/app/plan', name: 'app_plan')]
    #[Route('/login-link', name: 'login_link_public')]
    public function home(
        Environment $twig,
        SettingsRepositoryInterface $settingsRepository,
        LoggerInterface $logger,
    ): Response {
        $logger->info('A request was made to the frontend controller');
        try {
            $settingsRepository->getDefaultSettings();
        } catch (TableNotFoundException) {
            $logger->info('Redirected to install page');

            return new RedirectResponse('/install');
        }

        return new Response($twig->render('index.html.twig'));
    }

    #[Route('/error/stripe', name: 'app_site_error')]
    #[Route('/error/stripe-invalid', name: 'app_site_error_invalid')]
    public function stripeError(
        Environment $twig,
        LoggerInterface $logger,
    ): Response {
        $logger->warning('A user has ended up on the stripe error page');

        return new Response($twig->render('index.html.twig'));
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
