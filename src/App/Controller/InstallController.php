<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller;

use App\Database\TransactionManager;
use App\Entity\User;
use App\Install\DatabaseCreator;
use App\Install\Dto\InstallRequest;
use App\Install\Steps\BrandStep;
use App\Install\Steps\DataStep;
use App\Install\Steps\SystemSettingsStep;
use App\Install\Steps\TemplateStep;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Parthenon\User\Creator\UserCreatorInterface;
use Stripe\Account;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class InstallController
{
    use ValidationErrorResponseTrait;

    #[Route('/install', name: 'app_install', requirements: ['vueRouting' => '.+'], defaults: ['vueRouting' => null])]
    public function installDefault(
        Environment $twig,
        SettingsRepositoryInterface $settingsRepository,
        #[Autowire('%parthenon_billing_payments_obol_config%')]
        $stripeConfig, ): Response
    {
        try {
            Stripe::setApiKey($stripeConfig['api_key']);
            Account::retrieve();
        } catch (ApiErrorException $e) {
            return new RedirectResponse('/error/stripe-invalid');
        }

        try {
            $settings = $settingsRepository->getDefaultSettings();
        } catch (TableNotFoundException $exception) {
            return new Response($twig->render('index.html.twig'));
        }

        return new RedirectResponse('/login');
    }

    #[Route('/install/process', name: 'app_install_post', methods: ['POST'])]
    public function procesInstall(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        DatabaseCreator $creator,
        SystemSettingsStep $systemSettingsStep,
        BrandStep $brandStep,
        TemplateStep $templateStep,
        DataStep $dataStep,
        UserCreatorInterface $userCreator,
        UserRepositoryInterface $userRepository,
        TransactionManager $transactionManager
    ): Response {
        /** @var InstallRequest $dto */
        $dto = $serializer->deserialize($request->getContent(), InstallRequest::class, 'json');
        $errors = $validator->validate($dto);

        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse) {
            return $errorResponse;
        }
        $transactionManager->start();
        try {
            $creator->createDbSchema();
            $systemSettingsStep->install($dto);
            $brandStep->install($dto);
            $templateStep->install();
            $dataStep->install();

            $user = new User();
            $user->setEmail($dto->getEmail());
            $user->setPassword($dto->getPassword());
            $userCreator->create($user);

            $user->setIsConfirmed(true);
            $user->setRoles([User::ROLE_ADMIN]);
            $userRepository->save($user);
            $transactionManager->finish();
        } catch (\Throwable $e) {
            $transactionManager->abort();

            return new JsonResponse(['message' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([]);
    }
}
