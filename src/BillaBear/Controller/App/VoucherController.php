<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\VoucherAmountDataMapper;
use BillaBear\DataMappers\VoucherDataMapper;
use BillaBear\Dto\Request\App\Voucher\CreateVoucher;
use BillaBear\Dto\Response\Api\ListResponse;
use BillaBear\Dto\Response\App\Vouchers\VoucherView;
use BillaBear\Entity\Voucher;
use BillaBear\Repository\VoucherRepositoryInterface;
use BillaBear\User\UserProvider;
use BillaBear\Voucher\VoucherRegister;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VoucherController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/voucher', name: 'app_app_voucher_listvoucher', methods: ['GET'])]
    public function listVoucher(
        Request $request,
        SerializerInterface $serializer,
        VoucherDataMapper $voucherFactory,
        VoucherRepositoryInterface $voucherRepository,
    ): Response {
        $this->getLogger()->info('Received request to list vouchers');

        $lastKey = $request->get('last_key');
        $resultsPerPage = (int) $request->get('limit', 10);

        if ($resultsPerPage < 1) {
            return new JsonResponse([
                'reason' => 'limit is below 1',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($resultsPerPage > 100) {
            return new JsonResponse([
                'reason' => 'limit is above 100',
            ], JsonResponse::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }
        // TODO add filters
        $filters = [];

        $resultSet = $voucherRepository->getList(
            filters: $filters,
            limit: $resultsPerPage,
            lastId: $lastKey,
        );

        $dtos = array_map([$voucherFactory, 'createAppDto'], $resultSet->getResults());

        $listResponse = new ListResponse();
        $listResponse->setHasMore($resultSet->hasMore());
        $listResponse->setData($dtos);
        $listResponse->setLastKey($resultSet->getLastKey());

        $json = $serializer->serialize($listResponse, 'json');

        return new JsonResponse($json, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/voucher/create')]
    public function createVoucherData(
        PriceRepositoryInterface $priceRepository,
    ): Response {
        $this->getLogger()->info('Received request to read create voucher');

        $prices = $priceRepository->getAll();

        $currencies = [];
        foreach ($prices as $price) {
            if ($price->isDeleted()) {
                continue;
            }

            $currencies[] = $price->getCurrency();
        }
        $currencies = array_unique($currencies);
        sort($currencies);

        return new JsonResponse(['currencies' => $currencies]);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/voucher', name: 'app_app_voucher_createvoucher', methods: ['POST'])]
    public function createVoucher(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        VoucherRepositoryInterface $voucherRepository,
        VoucherDataMapper $voucherFactory,
        UserProvider $userProvider,
        VoucherRegister $voucherRegister,
    ) {
        $this->getLogger()->info('Received request to write create voucher');

        $createVoucher = $serializer->deserialize($request->getContent(), CreateVoucher::class, 'json');
        $errors = $validator->validate($createVoucher);
        $errorResponse = $this->handleErrors($errors);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $entity = $voucherFactory->createEntity($createVoucher);
        $entity->setBillingAdmin($userProvider->getUser());
        $voucherRegister->register($entity);
        $voucherRepository->save($entity);
        $dto = $voucherFactory->createAppDto($entity);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, json: true);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/voucher/{id}/disable', name: 'app_app_voucher_disablevoucher', methods: ['POST'])]
    public function disableVoucher(
        Request $request,
        VoucherRepositoryInterface $voucherRepository,
    ): Response {
        $this->getLogger()->info('Received request to disable voucher', ['voucher_id' => $request->get('id')]);

        try {
            /** @var Voucher $voucher */
            $voucher = $voucherRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $voucher->setDisabled(true);
        $voucherRepository->save($voucher);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/voucher/{id}/enable', name: 'app_app_voucher_enablevoucher', methods: ['POST'])]
    public function enableVoucher(
        Request $request,
        VoucherRepositoryInterface $voucherRepository,
    ): Response {
        $this->getLogger()->info('Received request to enable voucher', ['voucher_id' => $request->get('id')]);

        try {
            /** @var Voucher $voucher */
            $voucher = $voucherRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $voucher->setDisabled(false);
        $voucherRepository->save($voucher);

        return new JsonResponse([], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/app/voucher/{id}', name: 'app_app_voucher_viewvoucher', methods: ['GET'])]
    public function viewVoucher(
        Request $request,
        VoucherRepositoryInterface $voucherRepository,
        VoucherDataMapper $voucherFactory,
        VoucherAmountDataMapper $voucherAmountFactory,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received request to view voucher', ['voucher_id' => $request->get('id')]);

        try {
            /** @var Voucher $voucher */
            $voucher = $voucherRepository->getById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $voucherFactory->createAppDto($voucher);
        $view = new VoucherView();
        $view->setVoucher($dto);
        $view->setAmounts(array_map([$voucherAmountFactory, 'createAppDto'], $voucher->getAmounts()->toArray()));

        $json = $serializer->serialize($view, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
