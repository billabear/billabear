<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Entity\Subscription;
use Easybill\ZUGFeRD2\Builder;
use Easybill\ZUGFeRD2\Model\Amount;
use Easybill\ZUGFeRD2\Model\CrossIndustryInvoice;
use Easybill\ZUGFeRD2\Model\DateTime;
use Easybill\ZUGFeRD2\Model\DocumentContextParameter;
use Easybill\ZUGFeRD2\Model\DocumentLineDocument;
use Easybill\ZUGFeRD2\Model\ExchangedDocument;
use Easybill\ZUGFeRD2\Model\ExchangedDocumentContext;
use Easybill\ZUGFeRD2\Model\HeaderTradeAgreement;
use Easybill\ZUGFeRD2\Model\HeaderTradeDelivery;
use Easybill\ZUGFeRD2\Model\HeaderTradeSettlement;
use Easybill\ZUGFeRD2\Model\Id;
use Easybill\ZUGFeRD2\Model\LineTradeAgreement;
use Easybill\ZUGFeRD2\Model\LineTradeDelivery;
use Easybill\ZUGFeRD2\Model\LineTradeSettlement;
use Easybill\ZUGFeRD2\Model\Period;
use Easybill\ZUGFeRD2\Model\Quantity;
use Easybill\ZUGFeRD2\Model\SupplyChainEvent;
use Easybill\ZUGFeRD2\Model\SupplyChainTradeLineItem;
use Easybill\ZUGFeRD2\Model\SupplyChainTradeTransaction;
use Easybill\ZUGFeRD2\Model\TaxRegistration;
use Easybill\ZUGFeRD2\Model\TradeAddress;
use Easybill\ZUGFeRD2\Model\TradeContact;
use Easybill\ZUGFeRD2\Model\TradeParty;
use Easybill\ZUGFeRD2\Model\TradePaymentTerms;
use Easybill\ZUGFeRD2\Model\TradePrice;
use Easybill\ZUGFeRD2\Model\TradeProduct;
use Easybill\ZUGFeRD2\Model\TradeSettlementHeaderMonetarySummation;
use Easybill\ZUGFeRD2\Model\TradeSettlementLineMonetarySummation;
use Easybill\ZUGFeRD2\Model\TradeTax;
use Easybill\ZUGFeRD2\Model\UniversalCommunication;

class ZUGFeRDV2Formatter implements InvoiceFormatterInterface
{
    public const string FORMAT_NAME = 'app.invoices.delivery.format.zugferd_v2';

    public function generate(Invoice $invoice): string
    {
        $document = new CrossIndustryInvoice();
        $document->exchangedDocumentContext = new ExchangedDocumentContext();
        $document->exchangedDocumentContext->documentContextParameter = new DocumentContextParameter();
        $document->exchangedDocumentContext->documentContextParameter->id = Builder::GUIDELINE_SPECIFIED_DOCUMENT_CONTEXT_ID_XRECHNUNG;

        $document->exchangedDocument = new ExchangedDocument();
        $document->exchangedDocument->id = $invoice->getInvoiceNumber();
        $document->exchangedDocument->typeCode = '380';
        $document->exchangedDocument->issueDateTime = DateTime::create(102, $invoice->getCreatedAt()->format('Ymd'));

        $document->supplyChainTradeTransaction = new SupplyChainTradeTransaction();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement = new HeaderTradeAgreement();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerReference = $invoice->getInvoiceNumber();
        $document->supplyChainTradeTransaction->applicableHeaderTradeDelivery = new HeaderTradeDelivery();
        $document->supplyChainTradeTransaction->applicableHeaderTradeDelivery->chainEvent = new SupplyChainEvent();
        $document->supplyChainTradeTransaction->applicableHeaderTradeDelivery->chainEvent->date = DateTime::create(102, $invoice->getCreatedAt()->format('Ymd'));

        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement = new HeaderTradeSettlement();
        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement->currency = $invoice->getCurrency();
        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement->specifiedTradeSettlementHeaderMonetarySummation = $monetarySummation = new TradeSettlementHeaderMonetarySummation();

        $monetarySummation->taxBasisTotalAmount[] = Amount::create((string) $invoice->getSubTotalMoney()->getAmount());
        $monetarySummation->taxTotalAmount[] = Amount::create((string) $invoice->getVatTotalMoney()->getAmount());
        $monetarySummation->grandTotalAmount[] = Amount::create((string) $invoice->getTotalMoney()->getAmount());
        $monetarySummation->duePayableAmount = Amount::create((string) $invoice->getTotalMoney()->getAmount());
        $monetarySummation->lineTotalAmount = Amount::create((string) $invoice->getTotalMoney()->getAmount());

        $this->addBillingPeriod($invoice, $document);
        $this->addPaymentTerms($invoice, $document);
        $this->buildSeller($invoice, $document);
        $this->buildBuyer($invoice, $document);
        $this->addLines($invoice, $document);
        $this->addTaxHeaders($invoice, $document);

        return Builder::create()->transform($document);
    }

    public function filename(Invoice $invoice): string
    {
        return sprintf('invoice-%s.xml', $invoice->getInvoiceNumber());
    }

    private function buildSeller(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        $brand = $invoice->getBrandSettings();

        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty = $sellerTradeParty = new TradeParty();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->name = $brand->getBrandName();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress = new TradeAddress();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->lineOne = $brand->getAddress()->getStreetLineOne();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->lineTwo = $brand->getAddress()->getStreetLineTwo();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->city = $brand->getAddress()->getCity();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->countryCode = $brand->getAddress()->getCountry();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->postalTradeAddress->postcode = $brand->getAddress()->getPostcode();
        if ($brand->getTaxNumber()) {
            $taxRegistration = TaxRegistration::create($brand->getTaxNumber(), 'VA');
            $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->sellerTradeParty->taxRegistrations[] = $taxRegistration;
        }

        $sellerTradeParty->definedTradeContact = new TradeContact();
        $sellerTradeParty->definedTradeContact->personName = 'Support';
        $sellerTradeParty->definedTradeContact->emailURIUniversalCommunication = new UniversalCommunication();
        $sellerTradeParty->definedTradeContact->emailURIUniversalCommunication->uriid = Id::create($invoice->getBrandSettings()->getEmailAddress());
    }

    private function buildBuyer(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        $customer = $invoice->getCustomer();

        $customerName = (string) $customer->getName();
        if (empty($customerName)) {
            $customerName = $customer->getBillingEmail();
        }

        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty = new TradeParty();
        $document->supplyChainTradeTransaction->applicableHeaderTradeAgreement->buyerTradeParty->name = $customerName;
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

    private function addLines(Invoice $invoice, CrossIndustryInvoice $document): void
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
            $item->specifiedTradeProduct = new TradeProduct();
            $item->specifiedTradeProduct->name = $line->getDescription();
            $item->specifiedTradeProduct->sellerAssignedID = (string) $id;

            $item->tradeAgreement = new LineTradeAgreement();
            $item->tradeAgreement->netPrice = TradePrice::create((string) $line->getNetPriceAsMoney()->getAmount(), Quantity::create((string) $line->getQuantity(), 'H87'));
            $item->tradeAgreement->grossPrice = TradePrice::create((string) $line->getTotalMoney()->getAmount());

            $item->delivery = new LineTradeDelivery();
            $item->delivery->billedQuantity = Quantity::create($line->getQuantity(), 'H87');

            $item->specifiedLineTradeSettlement = new LineTradeSettlement();
            $item->specifiedLineTradeSettlement->tradeTax[] = $tradeTax = new TradeTax();

            $tradeTax->typeCode = 'VAT';
            $tradeTax->categoryCode = $this->getTaxCategoryCode($line);
            $tradeTax->rateApplicablePercent = $line->getTaxPercentage();

            $item->specifiedLineTradeSettlement->monetarySummation = TradeSettlementLineMonetarySummation::create($line->getTotalMoney()->getAmount());

            $document->supplyChainTradeTransaction->lineItems[] = $item;
            ++$lineNumber;
        }
    }

    private function addTaxHeaders(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        foreach ($invoice->getLines() as $line) {
            $tax = new TradeTax();
            $tax->typeCode = 'VAT';
            $tax->categoryCode = $this->getTaxCategoryCode($line);
            $tax->rateApplicablePercent = $line->getTaxPercentage();
            $tax->basisAmount = Amount::create((string) $line->getSubTotalMoney()->getAmount());
            $tax->calculatedAmount = Amount::create((string) $line->getVatTotalMoney()->getAmount());

            $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement->tradeTaxes[] = $tax;
        }
    }

    public function supports(string $type): bool
    {
        return self::FORMAT_NAME === $type;
    }

    public function name(): string
    {
        return self::FORMAT_NAME;
    }

    private function getTaxCategoryCode(InvoiceLine $line): string
    {
        if ($line->isReverseCharge()) {
            return 'AE';
        }

        if (0 === $line->getTaxPercentage()) {
            return 'G';
        }

        if ($line->isZeroRated()) {
            return 'Z';
        }

        return 'S';
    }

    private function addBillingPeriod(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        $startDate = null;
        /** @var Subscription $subscriptionItem */
        foreach ($invoice->getSubscriptions() as $subscriptionItem) {
            if ($subscriptionItem->getStartOfCurrentPeriod() < $startDate || null === $startDate) {
                $startDate = $subscriptionItem->getStartOfCurrentPeriod();
            }
        }

        if (null === $startDate) {
            return;
        }

        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement->billingSpecifiedPeriod = $billingPeriod = new Period();
        $billingPeriod->startDatetime = DateTime::create(102, $startDate->format('Ymd'));
        $billingPeriod->endDatetime = DateTime::create(102, $invoice->getCreatedAt()->format('Ymd'));
    }

    private function addPaymentTerms(Invoice $invoice, CrossIndustryInvoice $document): void
    {
        $document->supplyChainTradeTransaction->applicableHeaderTradeSettlement->specifiedTradePaymentTerms[] = $paymentTerms = new TradePaymentTerms();

        if (Customer::BILLING_TYPE_INVOICE == $invoice->getCustomer()->getBillingType()) {
            $dueDate = $invoice->getDueAt()->format('d.m.Y');
            $paymentTerms->description = 'Zahlbar innerhalb 30 Tagen netto bis '.$dueDate;
        } else {
            $paymentTerms->description = 'Sofort zahlbar mit Karte';
        }
    }
}
