<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace App\Quotes;

use App\Dto\Request\App\Invoice\CreateInvoiceItem;
use App\Dto\Request\App\Quote\CreateQuote;
use App\Dto\Request\App\Quote\CreateQuoteSubscription;
use App\Entity\Customer;
use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Event\QuoteCreated;
use App\Invoice\Pricer;
use App\Repository\CustomerRepositoryInterface;
use App\Repository\QuoteRepositoryInterface;
use App\Repository\TaxTypeRepositoryInterface;
use Brick\Money\Money;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class QuoteCreator
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private QuoteRepositoryInterface $quoteRepository,
        private Pricer $pricer,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
        private TaxTypeRepositoryInterface $taxTypeRepository,
    ) {
    }

    public function createQuote(CreateQuote $createQuote): Quote
    {
        /** @var Customer $customer */
        $customer = $this->customerRepository->getById($createQuote->getCustomer());
        $user = $this->security->getUser();
        $quote = new Quote();
        $quote->setCreatedBy($user);
        $quote->setCustomer($customer);
        if ($createQuote->getExpiresAt()) {
            $expiresAt = \DateTime::createFromFormat(\DATE_RFC3339_EXTENDED, $createQuote->getExpiresAt());
            $quote->setExpiresAt($expiresAt);
        }
        $lines = [];
        $totalAmount = null;
        $totalVat = null;
        $subTotal = null;
        /** @var CreateQuoteSubscription $subscription */
        foreach ($createQuote->getSubscriptions() as $subscription) {
            /** @var \App\Entity\SubscriptionPlan $plan */
            $plan = $this->subscriptionPlanRepository->getById($subscription->getPlan());
            /** @var \App\Entity\Price $price */
            $price = $this->priceRepository->getById($subscription->getPrice());

            $priceInfo = $this->pricer->getCustomerPriceInfo($price, $customer, $plan->getProduct()->getTaxType(), $subscription->getSeatNumber() ?? 1);

            $quoteLine = new QuoteLine();
            $quoteLine->setSubscriptionPlan($plan);
            $quoteLine->setPrice($price);
            $quoteLine->setSeatNumber($subscription->getSeatNumber());
            $quoteLine->setQuote($quote);
            $quoteLine->setTaxType($plan->getProduct()->getTaxType());
            $quoteLine->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
            $quoteLine->setTotal($priceInfo->total->getMinorAmount()->toInt());
            $quoteLine->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $quoteLine->setTaxPercentage($priceInfo->taxInfo->rate);
            $quoteLine->setTaxCountry($priceInfo->taxInfo->country);
            $quoteLine->setReverseCharge($priceInfo->taxInfo->reverseCharge);
            $quoteLine->setIncludeTax($price->isIncludingTax());
            $quoteLine->setCurrency($price->getCurrency());

            $totalAmount = $this->addAmount($totalAmount, $priceInfo->total);
            $totalVat = $this->addAmount($totalVat, $priceInfo->vat);
            $subTotal = $this->addAmount($subTotal, $priceInfo->subTotal);

            $quote->setCurrency($price->getCurrency());
            $lines[] = $quoteLine;
        }

        /** @var CreateInvoiceItem $item */
        foreach ($createQuote->getItems() as $item) {
            $quoteLine = new QuoteLine();
            $taxType = $this->taxTypeRepository->findById($item->getTaxType());
            $money = Money::ofMinor($item->getAmount(), $item->getCurrency());
            $priceInfo = $this->pricer->getCustomerPriceInfoFromMoney($money, $customer, $item->getIncludeTax(), $taxType);

            $quoteLine = new QuoteLine();
            $quoteLine->setQuote($quote);
            $quoteLine->setTaxType($taxType);
            $quoteLine->setIncludeTax($item->getIncludeTax());
            $quoteLine->setDescription($item->getDescription());
            $quoteLine->setTaxTotal($priceInfo->vat->getMinorAmount()->toInt());
            $quoteLine->setTotal($priceInfo->total->getMinorAmount()->toInt());
            $quoteLine->setSubTotal($priceInfo->subTotal->getMinorAmount()->toInt());
            $quoteLine->setTaxPercentage($priceInfo->taxInfo->rate);
            $quoteLine->setTaxCountry($priceInfo->taxInfo->country);
            $quoteLine->setReverseCharge($priceInfo->taxInfo->reverseCharge);
            $quoteLine->setCurrency($item->getCurrency());

            $totalAmount = $this->addAmount($totalAmount, $priceInfo->total);
            $totalVat = $this->addAmount($totalVat, $priceInfo->vat);
            $subTotal = $this->addAmount($subTotal, $priceInfo->subTotal);

            $quote->setCurrency($item->getCurrency());
            $lines[] = $quoteLine;
        }

        if (0 === count($lines)) {
            throw new \Exception('There is no quote lines');
        }

        $quote->setLines($lines);
        $quote->setAmountDue($totalAmount->getMinorAmount()->toInt());
        $quote->setTotal($totalAmount->getMinorAmount()->toInt());
        $quote->setSubTotal($subTotal->getMinorAmount()->toInt());
        $quote->setTaxTotal($totalVat->getMinorAmount()->toInt());
        $quote->setCreatedAt(new \DateTime());
        $quote->setUpdatedAt(new \DateTime());

        $this->quoteRepository->save($quote);

        $this->eventDispatcher->dispatch(new QuoteCreated($quote), QuoteCreated::NAME);

        return $quote;
    }

    public function addAmount(?Money $originalAmount, Money $additionalMoney): Money
    {
        if (!$originalAmount) {
            return $additionalMoney;
        }

        return $originalAmount->plus($additionalMoney);
    }
}
