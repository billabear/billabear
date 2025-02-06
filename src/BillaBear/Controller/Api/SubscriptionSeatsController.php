<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\Dto\Request\Api\Subscription\AddSeats;
use BillaBear\Dto\Request\Api\Subscription\RemoveSeats;
use BillaBear\Entity\Subscription;
use BillaBear\Repository\SubscriptionRepositoryInterface;
use BillaBear\Subscription\UpdateAction\AddSeatToSubscription;
use BillaBear\Subscription\UpdateAction\RemoveSeatFromSubscription;
use BillaBear\Webhook\Outbound\Payload\Subscription\SubscriptionUpdatedPayload;
use BillaBear\Webhook\Outbound\WebhookDispatcherInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionSeatsController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger, private readonly WebhookDispatcherInterface $webhookDispatcher)
    {
    }

    #[Route('/api/v1/subscription/{id}/seats/add', name: 'app_api_subscriptionseats_addseat', methods: ['POST'])]
    public function addSeat(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionRepositoryInterface $subscriptionRepository,
        AddSeatToSubscription $addSeatToSubscription,
        ValidatorInterface $validator,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
        $this->getLogger()->info('Received API request to add seat subscription', [
            'subscription_id' => $request->get('id'),
            'customer_id' => (string) $subscription->getCustomer()->getId(),
        ]);

        $dto = $serializer->deserialize($request->getContent(), AddSeats::class, 'json');
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $addSeatToSubscription->addSeats($subscription, $dto->getSeats());
        $this->webhookDispatcher->dispatch(new SubscriptionUpdatedPayload($subscription));

        return new JsonResponse(['success' => true]);
    }

    #[Route('/api/v1/subscription/{id}/seats/remove', name: 'app_api_subscriptionseats_removeseat', methods: ['POST'])]
    public function removeSeat(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionRepositoryInterface $subscriptionRepository,
        RemoveSeatFromSubscription $removeSeatFromSubscription,
        ValidatorInterface $validator,
    ): JsonResponse|Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        $this->getLogger()->info('Received API request to remove seat subscription', [
            'subscription_id' => $request->get('id'),
            'customer_id' => (string) $subscription->getCustomer()->getId(),
        ]);
        $dto = $serializer->deserialize($request->getContent(), RemoveSeats::class, 'json');
        $dto->setSubscription($subscription);
        $errors = $validator->validate($dto);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $removeSeatFromSubscription->removeSeats($subscription, $dto->getSeats());
        $this->webhookDispatcher->dispatch(new SubscriptionUpdatedPayload($subscription));

        return new JsonResponse(['success' => true]);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
