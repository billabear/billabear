<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller;

use BillaBear\Database\TransactionManager;
use BillaBear\Entity\User;
use BillaBear\Install\DatabaseCreator;
use BillaBear\Install\Dto\InstallRequest;
use BillaBear\Install\Steps\BrandStep;
use BillaBear\Install\Steps\DataStep;
use BillaBear\Install\Steps\SystemSettingsStep;
use BillaBear\Install\Steps\TemplateStep;
use BillaBear\Repository\SettingsRepositoryInterface;
use BillaBear\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException as DoctrineTableException;
use Parthenon\User\Creator\UserCreatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class InstallController
{
    use ValidationErrorResponseTrait;

    #[Route('/install', name: 'app_install')]
    public function installDefault(
        Environment $twig,
        SettingsRepositoryInterface $settingsRepository,
    ): Response {
        try {
            $settingsRepository->getDefaultSettings();
        } catch (DoctrineTableException) {
            return new Response($twig->render('index.html.twig'));
        }

        return new RedirectResponse('/login');
    }

    #[Route('/install/process', name: 'app_install_post', methods: ['POST'])]
    public function processInstall(
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
        TransactionManager $transactionManager,
    ): Response {
        /** @var InstallRequest $dto */
        $dto = $serializer->deserialize($request->getContent(), InstallRequest::class, 'json');
        $errors = $validator->validate($dto);

        $errorResponse = $this->handleErrors($errors);
        $locale = $request->headers->get('Accept-Language', 'en-US');
        $locale = preg_split('~-|_~', $locale)[0];

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
            $user->setLocale($locale);
            $userCreator->create($user);

            $user->setIsConfirmed(true);
            $user->setRoles([User::ROLE_ADMIN]);
            $userRepository->save($user);
            $transactionManager->finish();
        } catch (\Throwable $e) {
            $transactionManager->abort();

            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([]);
    }
}
