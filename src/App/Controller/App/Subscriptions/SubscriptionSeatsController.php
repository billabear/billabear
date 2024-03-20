<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App\Subscriptions;

use App\Controller\ValidationErrorResponseTrait;
use App\Dto\Request\App\Subscription\ChangeSeats;
use App\Entity\Subscription;
use App\Repository\SubscriptionRepositoryInterface;
use App\Subscription\UpdateAction\SetSeatsFromSubscription;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionSeatsController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/subscription/{id}/seats/set', name: 'app_app_subscriptionseats_change_seats', methods: ['POST'])]
    public function changeSeats(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SetSeatsFromSubscription $setSeatsFromSubscription,
        ValidatorInterface $validator,
    ) {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $serializer->deserialize($request->getContent(), ChangeSeats::class, 'json');

        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $setSeatsFromSubscription->setSeats($subscription, $dto->getSeats());

        return new JsonResponse(['success' => true]);
    }
}
