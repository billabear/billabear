<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\DataMappers\CancellationDataMapper;
use BillaBear\Repository\CancellationRequestRepositoryInterface;
use BillaBear\Repository\ManageCustomerSessionRepositoryInterface;
use BillaBear\Workflow\Messenger\Messages\ProcessCancellationRequest;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class SubscriptionController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/public/subscription/{token}/{id}/cancel', name: 'app_public_subscription_cancel', methods: ['POST'])]
    public function cancelSubscription(
        Request $request,
        ManageCustomerSessionRepositoryInterface $manageCustomerSessionRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        CancellationDataMapper $cancellationRequestFactory,
        MessageBusInterface $messageBus,
    ): Response {
        try {
            $session = $manageCustomerSessionRepository->getByToken($request->get('token'));
        } catch (NoEntityFoundException) {
            $this->getLogger()->warning('Unable to find customer management session', ['token' => $request->get('token')]);

            return new JsonResponse([], status: Response::HTTP_NOT_FOUND);
        }

        try {
            $subscription = $subscriptionRepository->findById($request->get('id'));
        } catch (NoEntityFoundException) {
            return new JsonResponse(null, status: Response::HTTP_NOT_FOUND);
        }

        $this->getLogger()->info(
            'Received request to cancel subscription via portal',
            [
                'token' => $request->get('token'),
                'customer_id' => (string) $session->getCustomer()->getId(),
                'subscription_id' => $request->get('id'),
            ]
        );

        $now = new \DateTime();

        if ($session->getExpiresAt() < $now) {
            $this->getLogger()->warning(
                'Customer management Session has expired so can\'t cancel the subscription',
                [
                    'token' => $request->get('token'),
                    'customer_id' => (string) $session->getCustomer()->getId(),
                    'subscription_id' => $request->get('id'),
                ]
            );

            return new JsonResponse(['expired' => true], status: Response::HTTP_NOT_FOUND);
        }

        $expiresAt = new \DateTime('+5 minutes');
        $session->setExpiresAt($expiresAt);
        $session->setUpdatedAt($now);
        $manageCustomerSessionRepository->save($session);

        $cancellationRequest = $cancellationRequestFactory->getCancellationRequestEntity($subscription, null);

        $cancellationRequestRepository->save($cancellationRequest);

        try {
            $messageBus->dispatch(new ProcessCancellationRequest((string) $cancellationRequest->getId()));
        } catch (\Throwable $exception) {
            $cancellationRequestRepository->save($cancellationRequest);

            return new JsonResponse(['error' => $exception->getMessage(), 'class' => $exception::class], status: JsonResponse::HTTP_FAILED_DEPENDENCY);
        }

        $cancellationRequestRepository->save($cancellationRequest);

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
