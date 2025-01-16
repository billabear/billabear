<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pdf;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Template;
use BillaBear\Repository\TemplateRepositoryInterface;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Entity\ReceiptLine;
use Parthenon\Common\Address;
use Parthenon\Common\Pdf\GeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

class ReceiptPdfGenerator
{
    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        #[Autowire('@template_twig')]
        private Environment $templateTwig,
        private GeneratorInterface $pdfGenerator,
    ) {
    }

    public function generate(Receipt $receipt)
    {
        $customer = $receipt->getCustomer();

        if (!$customer instanceof Customer) {
            throw new \LogicException('Invalid customer type');
        }
        $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_RECEIPT, $customer->getLocale(), $customer->getBrand());

        if (!$template) {
            $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_RECEIPT, $customer->getLocale(), Customer::DEFAULT_BRAND);
        }

        if (!$template) {
            $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_RECEIPT, Customer::DEFAULT_LOCALE, Customer::DEFAULT_BRAND);
        }

        if (!$template) {
            throw new \Exception('Unable to find pdf template');
        }

        $twigTemplate = $this->templateTwig->createTemplate($template->getContent());
        $content = $this->templateTwig->render($twigTemplate, $this->getData($receipt));

        return $this->pdfGenerator->generate($content);
    }

    protected function getCustomerData(Customer $customer): array
    {
        return [
            'name' => $customer->getName(),
            'email' => $customer->getBillingEmail(),
        ];
    }

    protected function getBrandData(BrandSettings $brandSettings): array
    {
        return [
            'name' => $brandSettings->getBrandName(),
            'address' => $this->getAddress($brandSettings->getAddress()),
            'tax_number' => $brandSettings->getTaxNumber(),
        ];
    }

    protected function getAddress(Address $address): array
    {
        return [
            'company_name' => $address->getCompanyName(),
            'street_line_one' => $address->getStreetLineOne(),
            'street_line_two' => $address->getStreetLineTwo(),
            'city' => $address->getCity(),
            'region' => $address->getRegion(),
            'country' => $address->getCountry(),
            'postcode' => $address->getPostcode(),
        ];
    }

    private function getData(Receipt $receipt): array
    {
        $customer = $receipt->getCustomer();
        if (!$customer instanceof Customer) {
            throw new \Exception('Not customer entity');
        }

        return [
            'customer' => $this->getCustomerData($customer),
            'brand' => $this->getBrandData($customer->getBrandSettings()),
            'receipt' => $this->getReceiptData($receipt),
        ];
    }

    private function getReceiptData(Receipt $receipt): array
    {
        return [
            'id' => (string) $receipt->getId(),
            'number' => $receipt->getInvoiceNumber(),
            'total' => $receipt->getTotal(),
            'total_display' => (string) $receipt->getTotalMoney(),
            'sub_total' => $receipt->getSubTotal(),
            'tax_total' => $receipt->getVatTotal(),
            'currency' => $receipt->getCurrency(),
            'lines' => array_map([$this, 'getReceiptLine'], $receipt->getLines()->toArray()),
            'biller_address' => $this->getAddress($receipt->getBillerAddress()),
            'payee_address' => $this->getAddress($receipt->getPayeeAddress()),
            'created_at' => $receipt->getCreatedAt()->format(\DATE_ATOM),
        ];
    }

    private function getReceiptLine(ReceiptLine $receiptLine): array
    {
        return [
            'total' => $receiptLine->getTotal(),
            'total_display' => (string) $receiptLine->getTotalMoney(),
            'sub_total' => $receiptLine->getSubTotal(),
            'tax_total' => $receiptLine->getVatTotal(),
            'tax_percentage' => $receiptLine->getVatPercentage(),
            'description' => $receiptLine->getDescription(),
            'metadata' => $receiptLine->getMetadata(),
        ];
    }
}
