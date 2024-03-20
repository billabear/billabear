<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Checkout;

use App\Dto\Request\Api\Checkout\CreateCheckout as ApiRequest;
use App\Dto\Request\App\Checkout\CreateCheckout as AppRequest;
use App\Dto\Request\App\Checkout\CreateCheckoutSubscription;
use App\Dto\Request\App\Invoice\CreateInvoiceItem;
use App\Entity\Checkout;
use App\Entity\CheckoutLine;
use App\Entity\Customer;
use App\Event\CheckoutCreated;
use App\Invoice\Pricer;
use App\Repository\BrandSettingsRepositoryInterface;
use App\Repository\CheckoutRepositoryInterface;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
use Brick\Money\Money;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckoutCreator
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private CheckoutRepositoryInterface $checkoutRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private TaxTypeRepositoryInterface $taxTypeRepository,
        private Pricer $pricer,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createCheckout(AppRequest|ApiRequest $createCheckout): Checkout
    {
        $checkout = new Checkout();

        if ($createCheckout->getCustomer()) {
            /** @var Customer $customer */
            $customer = $this->customerRepository->getById($createCheckout->getCustomer());
            $checkout->setCustomer($customer);
        }

        if ($createCheckout->getSlug()) {
            $checkout->setSlug($createCheckout->getSlug());
        } else {
            $checkout->setSlug(bin2hex(random_bytes(48)));
        }

        if ($createCheckout instanceof AppRequest) {
            $user = $this->security->getUser();
            $checkout->setCreatedBy($user);
        }
        $checkout->setBrandSettings($this->brandSettingsRepository->getByCode($createCheckout->getBrand()));
        $checkout->setName($createCheckout->getName() ?? 'generated_'.bin2hex(random_bytes(16)));
        $checkout->setPermanent($createCheckout->isPermanent());
        if ($createCheckout->getExpiresAt()) {
            $expiresAt = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $createCheckout->getExpiresAt());
            $checkout->setExpiresAt($expiresAt);
        }
        $lines = [];
        $totalAmount = null;
        $totalVat = null;
        $subTotal = null;
        /** @var CreateCheckoutSubscription $subscription */
        foreach ($createCheckout->getSubscriptions() as $subscription) {
            /** @var \App\Entity\SubscriptionPlan $plan */
            $plan = $this->subscriptionPlanRepository->getById($subscription->getPlan());
            /** @var \App\Entity\Price $price */
            $price = $this->priceRepository->getById($subscription->getPrice());

            $checkoutLine = new CheckoutLine();
            $checkoutLine->setSubscriptionPlan($plan);
            $checkoutLine->setPrice($price);
            $checkoutLine->setSeatNumber($subscription->getSeatNumber());
            $checkoutLine->setCheckout($checkout);
            $checkoutLine->setTaxType($plan->getProduct()->getTaxType());
            $checkoutLine->setDescription($plan->getProduct()->getName().' / '.$price->getSchedule());
            if (isset($customer)) {
                $priceInfo = $this->pricer->getCustomerPriceInfo($price, $customer, $plan->getProduct()->getTaxType(), $subscription->getSeatNumber() ?? 1);
                $checkoutLine->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
                $checkoutLine->setTotal($priceInfo->total->getMinorAmount()->toInt());
                $checkoutLine->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
                $checkoutLine->setTaxPercentage($priceInfo->taxInfo->rate);
                $checkoutLine->setTaxCountry($priceInfo->taxInfo->country);
                $checkoutLine->setReverseCharge($priceInfo->taxInfo->reverseCharge);
                $totalAmount = $this->addAmount($totalAmount, $priceInfo->total);
                $totalVat = $this->addAmount($totalVat, $priceInfo->vat);
                $subTotal = $this->addAmount($subTotal, $priceInfo->subTotal);
            } else {
                $checkoutLine->setTotal($price->getAsMoney()->getMinorAmount()->toInt());
                $totalAmount = $this->addAmount($totalAmount, $price->getAsMoney());
            }

            $checkoutLine->setIncludeTax($price->isIncludingTax());
            $checkoutLine->setCurrency($price->getCurrency());

            $checkout->setCurrency($price->getCurrency());
            $lines[] = $checkoutLine;
        }

        /** @var CreateInvoiceItem $item */
        foreach ($createCheckout->getItems() as $item) {
            $taxType = $this->taxTypeRepository->findById($item->getTaxType());
            $money = Money::ofMinor($item->getAmount(), $item->getCurrency());

            $checkoutLine = new CheckoutLine();
            $checkoutLine->setCheckout($checkout);
            $checkoutLine->setTaxType($taxType);
            $checkoutLine->setIncludeTax($item->getIncludeTax());
            $checkoutLine->setDescription($item->getDescription());
            if (isset($customer)) {
                $priceInfo = $this->pricer->getCustomerPriceInfoFromMoney($money, $customer, $item->getIncludeTax(), $taxType);
                $checkoutLine->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
                $checkoutLine->setTotal($priceInfo->total->getMinorAmount()->toInt());
                $checkoutLine->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
                $checkoutLine->setTaxPercentage($priceInfo->taxInfo->rate);
                $checkoutLine->setTaxCountry($priceInfo->taxInfo->country);
                $checkoutLine->setReverseCharge($priceInfo->taxInfo->reverseCharge);

                $totalAmount = $this->addAmount($totalAmount, $priceInfo->total);
                $totalVat = $this->addAmount($totalVat, $priceInfo->vat);
                $subTotal = $this->addAmount($subTotal, $priceInfo->subTotal);
            } else {
                $checkoutLine->setTotal($money->getMinorAmount()->toInt());
                $totalAmount = $this->addAmount($totalAmount, $money);
            }

            $checkoutLine->setCurrency($item->getCurrency());

            $checkout->setCurrency($item->getCurrency());
            $lines[] = $checkoutLine;
        }

        if (0 === count($lines)) {
            throw new \Exception('There is no checkout lines');
        }

        $checkout->setLines($lines);
        $checkout->setAmountDue($totalAmount?->getMinorAmount()->toInt());
        $checkout->setTotal($totalAmount?->getMinorAmount()->toInt());
        $checkout->setSubTotal($subTotal?->getMinorAmount()->toInt());
        $checkout->setTaxTotal($totalVat?->getMinorAmount()->toInt());
        $checkout->setCreatedAt(new \DateTime());
        $checkout->setUpdatedAt(new \DateTime());

        $this->checkoutRepository->save($checkout);

        $this->eventDispatcher->dispatch(new CheckoutCreated($checkout), CheckoutCreated::NAME);

        return $checkout;
    }

    public function addAmount(?Money $originalAmount, Money $additionalMoney): Money
    {
        if (!$originalAmount) {
            return $additionalMoney;
        }

        return $originalAmount->plus($additionalMoney);
    }
}
