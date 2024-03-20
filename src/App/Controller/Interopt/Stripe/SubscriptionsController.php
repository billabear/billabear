<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\Interopt\Stripe;

use App\DataMappers\Interopt\Stripe\SubscriptionDataMapper;
use App\Dto\Interopt\Stripe\Models\ListModel;
use App\Dto\Interopt\Stripe\Requests\Subscriptions\CancelSubscription;
use App\Entity\Subscription;
use App\Filters\Interopt\Stripe\SubscriptionList;
use App\Repository\CancellationRequestRepositoryInterface;
use App\Repository\SubscriptionRepositoryInterface;
use App\Subscription\CancellationRequestProcessor;
use App\User\UserProvider;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionsController
{
    #[Route('/interopt/stripe/v1/subscriptions', name: 'app_interopt_stripe_subscriptions_list', methods: ['GET'])]
    public function listAction(
        Request $request,
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDataMapper $subscriptionDataMapper,
        SerializerInterface $serializer,
    ): Response {
        $firstId = $request->get('ending_before');
        $lastId = $request->get('starting_after');
        $limit = $request->get('limit', 25);

        $filterBuilder = new SubscriptionList();
        $filters = $filterBuilder->buildFilters($request);

        $subscriptions = $subscriptionRepository->getList($filters, limit: $limit, lastId: $lastId, firstId: $firstId);

        $subscriptionModels = array_map([$subscriptionDataMapper, 'createModel'], $subscriptions->getResults());

        $output = new ListModel();
        $output->setData($subscriptionModels);
        $output->setUrl($request->getUri());
        $output->setHasMore($subscriptions->hasMore());

        $json = $serializer->serialize($output, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/interopt/stripe/v1/subscriptions/{subscriptionId}', name: 'app_interopt_stripe_subscriptions_cancel', methods: ['DELETE'])]
    public function cancelSubscription(
        Request $request,
        \Parthenon\Billing\Repository\SubscriptionRepositoryInterface $subscriptionRepository,
        CancellationRequestRepositoryInterface $cancellationRequestRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CancellationRequestProcessor $cancellationRequestProcessor,
        \App\DataMappers\CancellationDataMapper $cancellationRequestFactory,
        SubscriptionDataMapper $subscriptionDataMapper,
        UserProvider $userProvider,
    ): Response {
        try {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRepository->findById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $exception) {
            throw new NoEntityFoundException();
        }

        /** @var CancelSubscription $dto */
        $dto = $serializer->deserialize($request->getContent(), CancelSubscription::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorOutput = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorOutput[$propertyPath] = $error->getMessage();
            }

            return new JsonResponse([
                'success' => false,
                'errors' => $errorOutput,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $cancellationRequest = $cancellationRequestFactory->getCancellationRequestForStripe($subscription, $dto);

        $cancellationRequestRepository->save($cancellationRequest);

        try {
            $cancellationRequestProcessor->process($cancellationRequest);
        } catch (\Throwable $exception) {
            $cancellationRequestRepository->save($cancellationRequest);

            return new JsonResponse(['error' => $exception->getMessage(), 'class' => get_class($exception)], status: JsonResponse::HTTP_FAILED_DEPENDENCY);
        }

        $cancellationRequestRepository->save($cancellationRequest);

        $dto = $subscriptionDataMapper->createModel($subscription);
        $data = $serializer->serialize($dto, 'json');

        return new JsonResponse($data, status: JsonResponse::HTTP_ACCEPTED, json: true);
    }
}
