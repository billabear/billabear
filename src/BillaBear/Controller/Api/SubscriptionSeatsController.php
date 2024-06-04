<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\Dto\Request\Api\Subscription\AddSeats;
use BillaBear\Dto\Request\Api\Subscription\RemoveSeats;
use BillaBear\Entity\Subscription;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\UpdateAction\AddSeatToSubscription;
use BillaBear\Subscription\UpdateAction\RemoveSeatFromSubscription;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionSeatsController
{
    use ValidationErrorResponseTrait;
    use LoggerAwareTrait;

    #[Route('/api/v1/subscription/{id}/seats/add', name: 'app_api_subscriptionseats_addseat', methods: ['POST'])]
    public function addSeat(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionRepositoryInterface $subscriptionRepository,
        AddSeatToSubscription $addSeatToSubscription,
        ValidatorInterface $validator,
    ) {
        $this->getLogger()->info('Received API request to add seat subscription', ['subscription_id' => $request->get('id')]);
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $serializer->deserialize($request->getContent(), AddSeats::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $addSeatToSubscription->addSeats($subscription, $dto->getSeats());

        return new JsonResponse(['success' => true]);
    }

    #[Route('/api/v1/subscription/{id}/seats/remove', name: 'app_api_subscriptionseats_removeseat', methods: ['POST'])]
    public function removeSeat(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionRepositoryInterface $subscriptionRepository,
        RemoveSeatFromSubscription $removeSeatFromSubscription,
        ValidatorInterface $validator,
    ) {
        $this->getLogger()->info('Received API request to remove seat subscription', ['subscription_id' => $request->get('id')]);
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $serializer->deserialize($request->getContent(), RemoveSeats::class, 'json');
        $dto->setSubscription($subscription);
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $removeSeatFromSubscription->removeSeats($subscription, $dto->getSeats());

        return new JsonResponse(['success' => true]);
    }
}
