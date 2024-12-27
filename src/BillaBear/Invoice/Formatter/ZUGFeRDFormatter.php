<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use Brick\Money\Money;
use Easybill\ZUGFeRD\Builder;
use Easybill\ZUGFeRD\Model\Date;
use Easybill\ZUGFeRD\Model\Document;
use Easybill\ZUGFeRD\Model\Trade\Agreement;
use Easybill\ZUGFeRD\Model\Trade\Amount;
use Easybill\ZUGFeRD\Model\Trade\Item\LineDocument;
use Easybill\ZUGFeRD\Model\Trade\Item\LineItem;
use Easybill\ZUGFeRD\Model\Trade\Item\Price;
use Easybill\ZUGFeRD\Model\Trade\Item\Product;
use Easybill\ZUGFeRD\Model\Trade\Item\Quantity;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeAgreement;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeDelivery;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeMonetarySummation;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeSettlement;
use Easybill\ZUGFeRD\Model\Trade\MonetarySummation;
use Easybill\ZUGFeRD\Model\Trade\Settlement;
use Easybill\ZUGFeRD\Model\Trade\Tax\TaxRegistration;
use Easybill\ZUGFeRD\Model\Trade\Tax\TradeTax;
use Easybill\ZUGFeRD\Model\Trade\Trade;
use Easybill\ZUGFeRD\Model\Trade\TradeParty;
use Parthenon\Common\Address;

readonly class ZUGFeRDFormatter implements InvoiceFormatterInterface
{
    public const string FORMAT_NAME = 'app.invoices.delivery.format.zugferd_v1';

    public function generate(Invoice $invoice): mixed
    {
        $document = new Document();
        $document->getHeader()->setId($invoice->getInvoiceNumber());
        $document->getHeader()->setDate($this->buildDate($invoice->getCreatedAt()));
        $trade = new Trade();

        $trade->setAgreement($this->buildAgreement($invoice));
        $trade->setSettlement($this->buildSettlement($invoice));

        $this->addLineItems($trade, $invoice);
        $document->setTrade($trade);

        return Builder::create()->getXML($document);
    }

    public function filename(Invoice $invoice): string
    {
        return sprintf('invoice-%s.xml', $invoice->getInvoiceNumber());
    }

    public function supports(string $type): bool
    {
        return self::FORMAT_NAME === $type;
    }

    public function name(): string
    {
        return self::FORMAT_NAME;
    }

    private function buildDate(\DateTime $dateTime): Date
    {
        return new Date($dateTime->format('Ymd'));
    }

    private function addLineItems(Trade $trade, Invoice $invoice): void
    {
        $lineItems = [];

        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            $item = new LineItem();

            $product = new Product((string) $line->getId(), $line->getDescription());
            $item->setProduct($product);
            $tradeAgreement = new SpecifiedTradeAgreement();
            $tradeAgreement->setNetPrice($this->buildPrice($line->getSubTotalMoney()));
            $tradeAgreement->setGrossPrice($this->buildPrice($line->getTotalMoney()));
            $item->setTradeAgreement($tradeAgreement);

            $delivery = new SpecifiedTradeDelivery(new Quantity('C62', 1));
            $item->setDelivery($delivery);

            $tradeTax = new TradeTax();
            $tradeTax->setCode('VAT');
            $tradeTax->setPercent($line->getTaxTotalAsMoney()->getAmount()->toFloat());
            $tradeTax->setBasisAmount(new Amount($line->getSubTotalMoney()->getAmount()->toFloat(), $line->getCurrency()));
            $tradeTax->setCalculatedAmount(new Amount($line->getTaxTotalAsMoney()->getAmount()->toFloat(), $line->getCurrency()));

            $settlement = new SpecifiedTradeSettlement();
            $moneySummary = new SpecifiedTradeMonetarySummation($line->getTotalMoney()->getAmount()->toFloat(), $line->getTotalMoney()->getCurrency());
            $settlement->setMonetarySummation($moneySummary)
                ->setTradeTax($tradeTax);

            $item->setSettlement($settlement);

            $lineDocument = new LineDocument((string) $line->getId());
            $item->setLineDocument($lineDocument);

            $trade->addLineItem($item);
        }
    }

    private function buildPrice(Money $money): Price
    {
        return new Price($money->getAmount()->toFloat(), $money->getCurrency());
    }

    private function buildAgreement(Invoice $invoice): Agreement
    {
        $agreement = new Agreement();
        $agreement->setBuyer($this->buildBuyer($invoice->getCustomer()));
        $agreement->setSeller($this->buildSeller($invoice->getCustomer()->getBrandSettings()));
        $agreement->setBuyerReference($invoice->getCustomer()->getReference());

        return $agreement;
    }

    private function buildBuyer(Customer $customer): TradeParty
    {
        $buyer = new TradeParty($customer->getBillingEmail(), $this->buildAddress($customer->getBillingAddress()));
        if ($customer->getTaxNumber()) {
            $taxReg = new TaxRegistration('VA', $customer->getTaxNumber());
            $buyer->addTaxRegistration($taxReg);
        }

        return $buyer;
    }

    private function buildSeller(BrandSettings $brandSettings): TradeParty
    {
        $seller = new TradeParty($brandSettings->getBrandName(), $this->buildAddress($brandSettings->getAddress()));
        if ($brandSettings->getTaxNumber()) {
            $taxReg = new TaxRegistration('VA', $brandSettings->getTaxNumber());
            $seller->addTaxRegistration($taxReg);
        }

        return $seller;
    }

    private function buildAddress(Address $address): \Easybill\ZUGFeRD\Model\Address
    {
        return new \Easybill\ZUGFeRD\Model\Address(
            $address->getPostcode(),
            $address->getStreetLineOne(),
            $address->getStreetLineTwo(),
            $address->getCity(),
            $address->getCountry(),
        );
    }

    private function buildSettlement(Invoice $invoice): Settlement
    {
        $settlement = new Settlement();
        $moneySummary = new MonetarySummation(
            $invoice->getSubTotalMoney()->getAmount()->toFloat(),
            0.00,
            0.00,
            $invoice->getSubTotalMoney()->getAmount()->toFloat(),
            $invoice->getVatTotalMoney()->getAmount()->toFloat(),
            $invoice->getTotalMoney()->getAmount()->toFloat(),
            $invoice->getCurrency(),
        );
        $moneySummary->setDuePayableAmount(new Amount($invoice->getTotalMoney()->getAmount()->toFloat(), $invoice->getCurrency()));
        $settlement->setMonetarySummation($moneySummary);

        return $settlement;
    }
}
