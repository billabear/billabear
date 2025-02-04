<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\QuoteDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use BillaBear\DataMappers\Tax\TaxTypeDataMapper;
use BillaBear\Dto\Request\App\Invoice\CreateInvoice;
use BillaBear\Dto\Request\App\Invoice\ReadQuoteView;
use BillaBear\Dto\Request\App\Quote\CreateQuote;
use BillaBear\Dto\Response\App\ListResponse;
use BillaBear\Dto\Response\App\Quote\ReadQuote;
use BillaBear\Entity\Quote;
use BillaBear\Filters\CustomerList;
use BillaBear\Quotes\QuoteCreator;
use BillaBear\Repository\QuoteRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuoteController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/quotes/{id}/view', name: 'app_app_quote_readquote', methods: ['GET'])]
    public function readQuote(
        Request $request,
        SerializerInterface $serializer,
        QuoteDataMapper $quoteDataMapper,
        QuoteRepositoryInterface $quoteRepository,
    ): Response {
        $this->getLogger()->info('Received request to read quote', ['quote_id' => $request->get('id')]);

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
        $this->getLogger()->info('Received request to read create quote');

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
        $this->getLogger()->info('Received request to write create quote');

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
        $this->getLogger()->info('Received request to list quotes');

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

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
