<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Controller\App;

use App\Controller\ValidationErrorResponseTrait;
use App\DataMappers\QuoteDataMapper;
use App\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use App\DataMappers\TaxTypeDataMapper;
use App\Dto\Request\App\Invoice\CreateInvoice;
use App\Dto\Request\App\Invoice\ReadQuoteView;
use App\Dto\Request\App\Quote\CreateQuote;
use App\Dto\Response\App\ListResponse;
use App\Dto\Response\App\Quote\ReadQuote;
use App\Entity\Quote;
use App\Filters\CustomerList;
use App\Quotes\QuoteCreator;
use App\Repository\QuoteRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
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

    #[Route('/app/quotes/{id}/view', name: 'app_app_quote_readquote', methods: ['GET'])]
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
        SubscriptionPlanDataMapper $subscriptionPlanFactory,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
    ) {
        $subscriptionPlans = $subscriptionPlanRepository->getAll();
        $subscriptionPlanDtos = array_map([$subscriptionPlanFactory, 'createAppDto'], $subscriptionPlans);

        $taxTypes = $taxTypeRepository->getAll();
        $taxTypeDtos = array_map([$taxTypeDataMapper, 'createAppDto'], $taxTypes);

        $readQuote = new ReadQuoteView();
        $readQuote->setSubscriptionPlans($subscriptionPlanDtos);
        $readQuote->setTaxTypes($taxTypeDtos);
        $json = $serializer->serialize($readQuote, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/quotes/create', name: 'app_app_quote_createquote', methods: ['POST'])]
    public function createQuote(
        Request $request,
        QuoteCreator $quoteCreator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        QuoteDataMapper $quoteDataMapper,
    ): Response {
        /** @var CreateInvoice $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateQuote::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response) {
            return $response;
        }

        $quote = $quoteCreator->createQuote($dto);
        $dto = $quoteDataMapper->createAppDto($quote);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/quotes', name: 'app_app_quote_listquotes', methods: ['GET'])]
    public function listQuotes(
        Request $request,
        QuoteRepositoryInterface $quoteRepository,
        SerializerInterface $serializer,
        QuoteDataMapper $quoteDataMapper,
    ): Response {
        $lastKey = $request->get('last_key');
        $firstKey = $request->get('first_key');
        $resultsPerPage = (int) $request->get('per_page', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'success' => false,
                'reason' => 'per_page is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        $filterBuilder = new CustomerList();
        $filters = $filterBuilder->buildFilters($request);

        $resultSet = $quoteRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
            firstId: $firstKey,
        );

        $dtos = array_map([$quoteDataMapper, 'createAppDto'], $resultSet->getResults());
        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());
        $listResponse->setFirstKey($resultSet->getFirstKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }
}
