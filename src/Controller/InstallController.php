<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller;

use App\Entity\User;
use App\Install\DatabaseCreator;
use App\Install\Dto\InstallRequest;
use App\Install\Steps\BrandStep;
use App\Install\Steps\SystemSettingsStep;
use App\Install\Steps\TemplateStep;
use App\Repository\SettingsRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Parthenon\User\Creator\UserCreatorInterface;
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
    public function installDefault(Environment $twig, SettingsRepositoryInterface $settingsRepository): Response
    {
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
        UserCreatorInterface $userCreator,
        UserRepositoryInterface $userRepository,
    ): Response {
        /** @var InstallRequest $dto */
        $dto = $serializer->deserialize($request->getContent(), InstallRequest::class, 'json');
        $errors = $validator->validate($dto);

        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse) {
            return $errorResponse;
        }

        $creator->createDbSchema();
        $systemSettingsStep->install($dto);
        $brandStep->install($dto);
        $templateStep->install();

        $user = new User();
        $user->setEmail($dto->getEmail());
        $user->setPassword($dto->getPassword());
        $userCreator->create($user);

        $user->setIsConfirmed(true);
        $user->setRoles([User::ROLE_ADMIN]);
        $userRepository->save($user);

        return new JsonResponse([]);
    }
}
