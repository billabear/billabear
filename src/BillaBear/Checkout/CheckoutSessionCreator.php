<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Checkout;

use BillaBear\Entity\Checkout;
use BillaBear\Entity\CheckoutSession;
use BillaBear\Entity\CheckoutSessionLine;
use BillaBear\Entity\Customer;
use BillaBear\Event\Checkout\CheckoutSessionCreated;
use BillaBear\Invoice\Pricer;
use BillaBear\Repository\CheckoutSessionRepositoryInterface;
use Brick\Money\Exception\MoneyException;
use Brick\Money\Money;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class CheckoutSessionCreator
{
    public function __construct(
        private CheckoutSessionRepositoryInterface $checkoutSessionRepository,
        private Pricer $pricer,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws MoneyException
     */
    public function createCheckoutSession(Checkout $checkout, Customer $customer): CheckoutSession
    {
        $checkoutSession = new CheckoutSession();
        $checkoutSession->setCustomer($customer);
        $checkoutSession->setCheckout($checkout);

        $lines = [];
        $totalAmount = null;
        $totalVat = null;
        $subTotal = null;
        foreach ($checkout->getLines() as $line) {
            $checkoutSessionLine = new CheckoutSessionLine();
            $money = Money::ofMinor($line->getTotal(), $line->getCurrency());
            $priceInfo = $this->pricer->getCustomerPriceInfoFromMoney($money, $customer, $line->isIncludeTax(), $line->getTaxType());

            $checkoutSessionLine->setCurrency($line->getCurrency());
            $checkoutSessionLine->setSubscriptionPlan($line->getSubscriptionPlan());
            $checkoutSessionLine->setPrice($line->getPrice());
            $checkoutSessionLine->setSeatNumber($line->getSeatNumber());
            $checkoutSessionLine->setCheckoutSession($checkoutSession);
            $checkoutSessionLine->setDescription($line->getDescription());
            $checkoutSessionLine->setTaxType($line->getTaxType());
            $checkoutSessionLine->setIncludeTax($line->isIncludeTax());
            $checkoutSessionLine->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
            $checkoutSessionLine->setTotal($priceInfo->total->getMinorAmount()->toInt());
            $checkoutSessionLine->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $checkoutSessionLine->setTaxPercentage($priceInfo->taxInfo->rate);
            $checkoutSessionLine->setTaxCountry($priceInfo->taxInfo->country);
            $checkoutSessionLine->setTaxState($priceInfo->taxInfo->state);
            $checkoutSessionLine->setReverseCharge($priceInfo->taxInfo->reverseCharge);

            $totalAmount = $this->addAmount($totalAmount, $priceInfo->total);
            $totalVat = $this->addAmount($totalVat, $priceInfo->vat);
            $subTotal = $this->addAmount($subTotal, $priceInfo->subTotal);
            $lines[] = $checkoutSessionLine;
        }
        $checkoutSession->setCurrency($checkout->getCurrency());
        $checkoutSession->setLines($lines);
        $checkoutSession->setAmountDue($totalAmount?->getMinorAmount()->toInt());
        $checkoutSession->setTotal($totalAmount?->getMinorAmount()->toInt());
        $checkoutSession->setSubTotal($subTotal?->getMinorAmount()->toInt());
        $checkoutSession->setTaxTotal($totalVat?->getMinorAmount()->toInt());
        $checkoutSession->setCreatedAt(new \DateTime());
        $checkoutSession->setUpdatedAt(new \DateTime());

        $this->checkoutSessionRepository->save($checkoutSession);

        $this->eventDispatcher->dispatch(new CheckoutSessionCreated($checkoutSession), CheckoutSessionCreated::NAME);

        return $checkoutSession;
    }

    /**
     * @throws MoneyException
     */
    private function addAmount(?Money $originalAmount, Money $additionalMoney): Money
    {
        if (!$originalAmount) {
            return $additionalMoney;
        }

        return $originalAmount->plus($additionalMoney);
    }
}
