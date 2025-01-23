<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App\Subscriptions;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\Dto\Request\App\Subscription\ChangeSeats;
use BillaBear\Entity\Subscription;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\UpdateAction\SetSeatsFromSubscription;
use BillaBear\Webhook\Outbound\Payload\Subscription\SubscriptionUpdatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
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

    public function __construct(private WebhookDispatcherInterface $webhookDispatcher)
    {
    }

    #[Route('/app/subscription/{id}/seats/set', name: 'app_app_subscriptionseats_change_seats', methods: ['POST'])]
    public function changeSeats(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SetSeatsFromSubscription $setSeatsFromSubscription,
        ValidatorInterface $validator,
    ) {
        $this->getLogger()->info('Received request to update subscription seats', ['subscription_id' => $request->get('id')]);

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
        $this->webhookDispatcher->dispatch(new SubscriptionUpdatedPayload($subscription));

        return new JsonResponse(['success' => true]);
    }
}
