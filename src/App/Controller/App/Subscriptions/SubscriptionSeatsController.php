<?php

/*
 * Copyright Humbly Arrogant Software Limited 2022-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
