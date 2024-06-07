<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\Repository\SettingsRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class FrontController
{
    use LoggerAwareTrait;

    #[Route('/portal/{vueRouting}', name: 'public_main', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/portal/pay/{hash}', name: 'portal_pay_invoice', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/portal/quote/{hash}', name: 'portal_pay_quote', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    #[Route('/portal/checkout/{slug}', name: 'portal_pay_checkout', methods: ['GET'])]
    public function handlePublic(
        Environment $twig,
        SettingsRepositoryInterface $settingsRepository,
        #[Autowire(env: 'STRIPE_PRIVATE_API_KEY')] $privateApiKey,
    ) {
        $this->getLogger()->info('Received request to handle public site');

        if (empty($privateApiKey)) {
            return new RedirectResponse('/error/stripe');
        }

        try {
            $settings = $settingsRepository->getDefaultSettings();
        } catch (TableNotFoundException $exception) {
            return new RedirectResponse('/error/stripe');
        }

        return new Response($twig->render('public.html.twig'));
    }
}
