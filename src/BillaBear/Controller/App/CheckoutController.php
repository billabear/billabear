<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Checkout\CheckoutCreator;
use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\CheckoutDataMapper;
use BillaBear\DataMappers\Settings\BrandSettingsDataMapper;
use BillaBear\DataMappers\Subscriptions\SubscriptionPlanDataMapper;
use BillaBear\DataMappers\TaxTypeDataMapper;
use BillaBear\Dto\Request\App\Checkout\CreateCheckout;
use BillaBear\Dto\Response\App\Checkout\ReadCheckout;
use BillaBear\Dto\Response\App\Checkout\ReadCreateCheckoutView;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\CheckoutRepositoryInterface;
use BillaBear\Repository\TaxTypeRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CheckoutController
{
    use CrudListTrait;
    use ValidationErrorResponseTrait;
    use LoggerAwareTrait;

    #[Route('/app/checkout', name: 'app_app_checkout_listcheckout', methods: ['GET'])]
    public function listCheckout(
        Request $request,
        CheckoutRepositoryInterface $repository,
        SerializerInterface $serializer,
        CheckoutDataMapper $factory,
    ): Response {
        $this->getLogger()->info('Received request to list checkout');

        return $this->crudList($request, $repository, $serializer, $factory);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/checkout/create', name: 'app_app_checkout_readcreatecheckoutinfo', methods: ['GET'])]
    public function readCreateCheckoutInfo(
        Request $request,
        SerializerInterface $serializer,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        SubscriptionPlanDataMapper $subscriptionPlanFactory,
        BrandSettingsRepositoryInterface $brandSettingsRepository,
        BrandSettingsDataMapper $brandSettingsDataMapper,
        TaxTypeRepositoryInterface $taxTypeRepository,
        TaxTypeDataMapper $taxTypeDataMapper,
    ) {
        $this->getLogger()->info('Received request to read create checkout');
        $subscriptionPlans = $subscriptionPlanRepository->getAll();
        $subscriptionPlanDtos = array_map([$subscriptionPlanFactory, 'createAppDto'], $subscriptionPlans);

        $brands = $brandSettingsRepository->getAll();
        $brandDtos = array_map([$brandSettingsDataMapper, 'createAppDto'], $brands);

        $taxTypes = $taxTypeRepository->getAll();
        $taxTypeDtos = array_map([$taxTypeDataMapper, 'createAppDto'], $taxTypes);

        $readQuote = new ReadCreateCheckoutView();
        $readQuote->setSubscriptionPlans($subscriptionPlanDtos);
        $readQuote->setBrands($brandDtos);
        $readQuote->setTaxTypes($taxTypeDtos);
        $json = $serializer->serialize($readQuote, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/checkout/create', name: 'app_app_checkout_createcheckout', methods: ['POST'])]
    public function createCheckout(
        Request $request,
        CheckoutCreator $checkoutCreator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CheckoutDataMapper $checkoutDataMapper,
    ): Response {
        $this->getLogger()->info('Received request to write create checkout');
        /** @var CreateCheckout $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateCheckout::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response) {
            return $response;
        }

        $quote = $checkoutCreator->createCheckout($dto);
        $dto = $checkoutDataMapper->createAppDto($quote);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/app/checkout/{id}/view', name: 'app_app_checkout_readcheckout', methods: ['GET'])]
    public function readCheckout(
        Request $request,
        CheckoutRepositoryInterface $checkoutRepository,
        CheckoutDataMapper $checkoutDataMapper,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to read checkout', ['checkout_id' => $request->get('id')]);

        try {
            $checkout = $checkoutRepository->findById($request->get('id'));
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $checkoutDataMapper->createAppDto($checkout);
        $viewDto = new ReadCheckout();
        $viewDto->setCheckout($dto);

        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }
}
