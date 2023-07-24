<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Controller\App;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\QuoteDataMapper;
use App\DataMappers\SubscriptionPlanFactory;
use App\Dto\Request\App\Invoice\CreateInvoice;
use App\Dto\Request\App\Invoice\ReadQuoteView;
use App\Dto\Response\App\Quote\ReadQuote;
use App\Entity\Quote;
use App\Quotes\QuoteCreator;
use App\Repository\QuoteRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuoteController
{
    use ValidationErrorResponseTrait;

    #[Route('/app/quotes/{id}', name: 'app_app_quote_readquote', methods: ['GET'])]
    public function readQuote(
        Request $request,
        SerializerInterface $serializer,
        QuoteDataMapper $quoteDataMapper,
        QuoteRepositoryInterface $quoteRepository,
    ): Response {
        try {
            /** @var Quote $quote */
            $quote = $quoteRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $quoteDto = $quoteDataMapper->createAppDto($quote);
        $dto = new ReadQuote();
        $dto->setQuote($quoteDto);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/quotes/create', name: 'app_app_quote_readquoteinfo', methods: ['GET'])]
    public function readCreateQuoteInfo(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionPlanFactory $subscriptionPlanFactory,
    ) {
        $subscriptionPlans = $subscriptionPlanRepository->getAll();
        $subscriptionPlanDtos = array_map([$subscriptionPlanFactory, 'createAppDto'], $subscriptionPlans);

        $readQuote = new ReadQuoteView();
        $readQuote->setSubscriptionPlans($subscriptionPlanDtos);
        $json = $serializer->serialize($readQuote, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/quotes/create', name: 'app_app_quote_createquote', methods: ['POST'])]
    public function createQuote(
        Request $request,
        QuoteCreator $quoteCreator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): Response {
        /** @var CreateInvoice $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateInvoice::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response) {
            return $response;
        }

        $quoteCreator->createQuote($dto);

        return new JsonResponse([]);
    }
}
