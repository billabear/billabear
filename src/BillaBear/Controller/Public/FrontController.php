<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\Repository\SettingsRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class FrontController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/portal/{vueRouting}', name: 'public_main', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/portal/pay/{hash}', name: 'portal_pay_invoice', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/portal/quote/{hash}', name: 'portal_pay_quote', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/portal/checkout/{slug}', name: 'portal_pay_checkout', methods: ['GET'])]
    public function handlePublic(
        Environment $twig,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        $this->getLogger()->info('Received request to handle public site');

        try {
            $settings = $settingsRepository->getDefaultSettings();
        } catch (TableNotFoundException $exception) {
            return new RedirectResponse('/error/stripe');
        }

        return new Response($twig->render('public.html.twig'));
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
