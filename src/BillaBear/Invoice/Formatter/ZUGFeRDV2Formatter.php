<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use Easybill\ZUGFeRD2\Builder;
use Easybill\ZUGFeRD2\Model\Amount;
use Easybill\ZUGFeRD2\Model\CrossIndustryInvoice;
use Easybill\ZUGFeRD2\Model\DateTime;
use Easybill\ZUGFeRD2\Model\DocumentContextParameter;
use Easybill\ZUGFeRD2\Model\DocumentLineDocument;
use Easybill\ZUGFeRD2\Model\ExchangedDocument;
use Easybill\ZUGFeRD2\Model\ExchangedDocumentContext;
use Easybill\ZUGFeRD2\Model\HeaderTradeAgreement;
use Easybill\ZUGFeRD2\Model\HeaderTradeSettlement;
use Easybill\ZUGFeRD2\Model\LineTradeAgreement;
use Easybill\ZUGFeRD2\Model\LineTradeDelivery;
use Easybill\ZUGFeRD2\Model\LineTradeSettlement;
use Easybill\ZUGFeRD2\Model\Quantity;
use Easybill\ZUGFeRD2\Model\SupplyChainTradeLineItem;
use Easybill\ZUGFeRD2\Model\SupplyChainTradeTransaction;
use Easybill\ZUGFeRD2\Model\TaxRegistration;
use Easybill\ZUGFeRD2\Model\TradeAddress;
use Easybill\ZUGFeRD2\Model\TradeParty;
use Easybill\ZUGFeRD2\Model\TradePrice;
use Easybill\ZUGFeRD2\Model\TradeSettlementHeaderMonetarySummation;
use Easybill\ZUGFeRD2\Model\TradeSettlementLineMonetarySummation;
use Easybill\ZUGFeRD2\Model\TradeTax;

class ZUGFeRDV2Formatter implements InvoiceFormatterInterface
{
    public function generate(Invoice $invoice): mixed
    {
        $document = new CrossIndustryInvoice();
        $document->exchangedDocumentContext = new ExchangedDocumentContext();
        $document->exchangedDocumentContext->documentContextParameter = new DocumentContextParameter();
        $document->exchangedDocumentContext->documentContextParameter->id = Builder::GUIDELINE_SPECIFIED_DOCUMENT_CONTEXT_ID_XRECHNUNG;

        $document->exchangedDocument = new ExchangedDocument();
        $document->exchangedDocument->id = $invoice->getInvoiceNumber();
        $document->exchangedDocument->typeCode = '380';
        $document->exchangedDocument->issueDateTime = DateTime::create(102, $invoice->getCreatedAt()->format(\DateTime::ATOM));

        $document->supplyChainTradeTransaction = new SupplyChainTradeTransaction();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement = new HeaderTradeAgreement();

        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement = new HeaderTradeSettlement();
        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement->currency = 'EUR';
        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement->specifiedTradeSettlementHeaderMonetarySummation = $monetarySummation = new TradeSettlementHeaderMonetarySummation();

        $currency = $invoice->getTotalMoney()->getCurrency()->getCurrencyCode();

        $monetarySummation->taxBasisTotalAmount[] = Amount::create((string) $invoice->getSubTotalMoney()->getAmount(), $currency);
        $monetarySummation->taxTotalAmount[] = Amount::create((string) $invoice->getVatTotalMoney()->getAmount(), $currency);
        $monetarySummation->grandTotalAmount[] = Amount::create((string) $invoice->getTotalMoney()->getAmount(), $currency);
        $monetarySummation->duePayableAmount = Amount::create((string) $invoice->getTotalMoney()->getAmount(), $currency);

        $this->buildSeller($invoice, $document);
        $this->buildBuyer($invoice, $document);
        $this->addLines($invoice, $document);

        return Builder::create()->transform($document);
    }

    public function filename(Invoice $invoice): string
    {
        return sprintf('invoice-%s.xml', $invoice->getInvoiceNumber());
    }

    private function buildSeller(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        $brand = $invoice->getBrandSettings();

        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty = new TradeParty();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->name = $brand->getBrandName();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress = new TradeAddress();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->lineOne = $brand->getAddress()->getStreetLineOne();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->lineTwo = $brand->getAddress()->getStreetLineTwo();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->city = $brand->getAddress()->getCity();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->countryCode = $brand->getAddress()->getCountry();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->postcode = $brand->getAddress()->getPostcode();
        $taxRegistration = TaxRegistration::create($brand->getTaxNumber(), 'VA');
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->taxRegistrations[] = $taxRegistration;
    }

    private function buildBuyer(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        $customer = $invoice->getCustomer();

        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty = new TradeParty();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->name = $customer->getName();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->postalTradeAddress = new TradeAddress();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->postalTradeAddress->lineOne = $customer->getBillingAddress()->getStreetLineOne();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->postalTradeAddress->lineTwo = $customer->getBillingAddress()->getStreetLineTwo();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->postalTradeAddress->city = $customer->getBillingAddress()->getCity();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->postalTradeAddress->countryCode = $customer->getBillingAddress()->getCountry();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->postalTradeAddress->postcode = $customer->getBillingAddress()->getPostcode();

        if ($customer->getTaxNumber()) {
            $taxRegistration = TaxRegistration::create($customer->getTaxNumber(), 'VA');
            $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->taxRegistrations[] = $taxRegistration;
        }
    }

    public function addLines(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        $lineNumber = 1;
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            $item = new SupplyChainTradeLineItem();

            if (!$line->getProduct()) {
                $id = $line->getId();
            } else {
                $id = $line->getProduct()->getId();
            }

            $item->associatedDocumentLineDocument = DocumentLineDocument::create((string) $lineNumber);
            $item->specifiedTradeProduct = new \Easybill\ZUGFeRD2\Model\TradeProduct();
            $item->specifiedTradeProduct->name = $line->getDescription();
            $item->specifiedTradeProduct->sellerAssignedID = (string) $id;

            $item->tradeAgreement = new LineTradeAgreement();
            $item->tradeAgreement->netPrice = TradePrice::create((string) $line->getNetPriceAsMoney()->getAmount(), $line->getQuantity());
            $item->tradeAgreement->grossPrice = TradePrice::create((string) $line->getTotalMoney()->getAmount());

            $item->delivery = new LineTradeDelivery();
            $item->delivery->billedQuantity = Quantity::create($line->getQuantity(), 'H87');

            $item->specifiedLineTradeSettlement = new LineTradeSettlement();
            $item->specifiedLineTradeSettlement->tradeTax[] = $item1tax = new TradeTax();

            $item1tax->typeCode = 'VAT';
            $item1tax->categoryCode = 'S';
            $item1tax->rateApplicablePercent = $line->getTaxPercentage();

            $item->specifiedLineTradeSettlement->monetarySummation = TradeSettlementLineMonetarySummation::create($line->getTotalMoney()->getAmount());

            $document->supplyChainTradeTransaction->lineItems[] = $item;
            ++$lineNumber;
        }
    }
}
