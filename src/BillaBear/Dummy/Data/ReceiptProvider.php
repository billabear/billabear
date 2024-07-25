<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Dummy\Data;

use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Entity\Payment;
use BillaBear\Entity\Quote;
use BillaBear\Entity\QuoteLine;
use BillaBear\Entity\TaxType;
use Doctrine\Common\Collections\ArrayCollection;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Entity\ReceiptLine;
use Parthenon\Common\Address;

class ReceiptProvider
{
    use CustomerTrait;

    public function getDummyPayment(): Payment
    {
        $payment = new Payment();
        $payment->setState('paid');
        $payment->setAmount(30000);
        $payment->setCurrency('EUR');
        $payment->setCustomer($this->buildCustomer());
        $payment->setDescription('Dummy Payment');
        $payment->setCreatedAt(new \DateTime());

        return $payment;
    }

    public function getPaymentCard(): PaymentCard
    {
        $paymentCard = new PaymentCard();
        $paymentCard->setName('Card here');
        $paymentCard->setBrand('VISA');
        $paymentCard->setCustomer($this->buildCustomer());
        $paymentCard->setCreatedAt(new \DateTime());
        $paymentCard->setProvider('stripe');
        $paymentCard->setExpiryYear('29');
        $paymentCard->setExpiryMonth('10');
        $paymentCard->setLastFour('4242');

        return $paymentCard;
    }

    public function getDummyReceipt(): Receipt
    {
        $customer = $this->buildCustomer();

        $receipt = new Receipt();
        $receipt->setCreatedAt(new \DateTime('now'));
        $receipt->setCustomer($customer);

        $lineOne = new ReceiptLine();
        $lineOne->setReceipt($receipt);
        $lineOne->setCurrency('EUR');
        $lineOne->setTotal(10000);
        $lineOne->setSubTotal(8000);
        $lineOne->setVatTotal(2000);
        $lineOne->setVatPercentage(20);
        $lineOne->setDescription('Example Line One');

        $lineTwo = new ReceiptLine();
        $lineTwo->setReceipt($receipt);
        $lineTwo->setCurrency('EUR');
        $lineTwo->setTotal(20000);
        $lineTwo->setSubTotal(16000);
        $lineTwo->setVatTotal(4000);
        $lineTwo->setVatPercentage(20);
        $lineTwo->setDescription('Example Line Two');

        $receipt->setLines([$lineOne, $lineTwo]);
        $receipt->setTotal(30000);
        $receipt->setSubTotal(24000);
        $receipt->setVatTotal(6000);
        $receipt->setCurrency('EUR');
        $receipt->setValid(true);

        $payeeAddress = new Address();
        $payeeAddress->setCompanyName('Company One');
        $payeeAddress->setStreetLineOne('One Example Strasse');
        $payeeAddress->setRegion('Berlin');
        $payeeAddress->setCity('Berlin');
        $payeeAddress->setCountry('Germany');
        $payeeAddress->setPostcode('10366');

        $receipt->setPayeeAddress($payeeAddress);

        $billerAddress = new Address();
        $billerAddress->setCompanyName('Company One');
        $billerAddress->setStreetLineOne('Two Example Straße');
        $billerAddress->setRegion('Berlin');
        $billerAddress->setCity('Berlin');
        $billerAddress->setCountry('Germany');
        $billerAddress->setPostcode('10366');

        $receipt->setBillerAddress($billerAddress);

        $receipt->setInvoiceNumber('SKDLSk');

        return $receipt;
    }

    public function getInvoice(): Invoice
    {
        $customer = $this->buildCustomer();

        $taxType = new TaxType();
        $taxType->setName('name');

        $invoice = new Invoice();
        $invoice->setCreatedAt(new \DateTime('now'));
        $invoice->setCustomer($customer);

        $lineOne = new InvoiceLine();
        $lineOne->setInvoice($invoice);
        $lineOne->setCurrency('EUR');
        $lineOne->setTotal(10000);
        $lineOne->setSubTotal(8000);
        $lineOne->setTaxTotal(2000);
        $lineOne->setDescription('Example Line One');
        $lineOne->setTaxCountry('DE');
        $lineOne->setTaxType($taxType);

        $lineTwo = new InvoiceLine();
        $lineTwo->setInvoice($invoice);
        $lineTwo->setCurrency('EUR');
        $lineTwo->setTotal(20000);
        $lineTwo->setSubTotal(16000);
        $lineTwo->setTaxTotal(4000);
        $lineTwo->setDescription('Example Line Two');
        $lineTwo->setTaxCountry('DE');
        $lineTwo->setTaxType($taxType);

        $invoice->setLines(new ArrayCollection([$lineOne, $lineTwo]));
        $invoice->setTotal(30000);
        $invoice->setSubTotal(24000);
        $invoice->setTaxTotal(6000);
        $invoice->setCurrency('EUR');
        $invoice->setValid(true);

        $payeeAddress = new Address();
        $payeeAddress->setCompanyName('Company One');
        $payeeAddress->setStreetLineOne('One Example Strasse');
        $payeeAddress->setRegion('Berlin');
        $payeeAddress->setCity('Berlin');
        $payeeAddress->setCountry('Germany');
        $payeeAddress->setPostcode('10366');

        $invoice->setPayeeAddress($payeeAddress);

        $billerAddress = new Address();
        $billerAddress->setCompanyName('Company One');
        $billerAddress->setStreetLineOne('Two Example Straße');
        $billerAddress->setRegion('Berlin');
        $billerAddress->setCity('Berlin');
        $billerAddress->setCountry('Germany');
        $billerAddress->setPostcode('10366');

        $invoice->setBillerAddress($billerAddress);

        $invoice->setInvoiceNumber('SKDLSk');

        return $invoice;
    }

    public function getQuote()
    {
        $customer = $this->buildCustomer();
        $quote = new Quote();
        $taxType = new TaxType();
        $taxType->setName('name');

        $quote->setCreatedAt(new \DateTime('now'));
        $quote->setCustomer($customer);

        $lineOne = new QuoteLine();
        $lineOne->setQuote($quote);
        $lineOne->setCurrency('EUR');
        $lineOne->setTotal(10000);
        $lineOne->setSubTotal(8000);
        $lineOne->setTaxTotal(2000);
        $lineOne->setDescription('Example Line One');
        $lineOne->setTaxCountry('DE');
        $lineOne->setTaxType($taxType);

        $lineTwo = new QuoteLine();
        $lineTwo->setQuote($quote);
        $lineTwo->setCurrency('EUR');
        $lineTwo->setTotal(20000);
        $lineTwo->setSubTotal(16000);
        $lineTwo->setTaxTotal(4000);
        $lineTwo->setDescription('Example Line Two');
        $lineTwo->setTaxCountry('DE');
        $lineTwo->setTaxType($taxType);

        $quote->setLines(new ArrayCollection([$lineOne, $lineTwo]));
        $quote->setTotal(30000);
        $quote->setSubTotal(24000);
        $quote->setTaxTotal(6000);
        $quote->setCurrency('EUR');

        return $quote;
    }
}
