<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Pdf;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Entity\Template;
use App\Repository\TemplateRepositoryInterface;
use Parthenon\Common\Address;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Pdf\GeneratorInterface;
use Twig\Environment;

class InvoicePdfGenerator
{
    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        private Environment $twig,
        private GeneratorInterface $pdfGenerator,
    ) {
    }

    public function generate(Invoice $invoice)
    {
        $customer = $invoice->getCustomer();

        if (!$customer instanceof Customer) {
            throw new \LogicException('Invalid customer type');
        }
        try {
            $template = $this->templateRepository->getByNameAndBrand(Template::NAME_INVOICE, $customer->getBrand());
        } catch (NoEntityFoundException $exception) {
            $template = $this->templateRepository->getByNameAndBrand(Template::NAME_INVOICE, Customer::DEFAULT_BRAND);
        }

        $twigTemplate = $this->twig->createTemplate($template->getContent());
        $content = $this->twig->render($twigTemplate, $this->getData($invoice));

        return $this->pdfGenerator->generate($content);
    }

    private function getData(Invoice $invoice): array
    {
        return [
            'customer' => $this->getCustomerData($invoice->getCustomer()),
            'brand' => $this->getBrandData($invoice->getCustomer()->getBrandSettings()),
            'invoice' => $this->getInvoiceData($invoice),
        ];
    }

    protected function getCustomerData(Customer $customer): array
    {
        return [
            'name' => $customer->getName(),
            'email' => $customer->getBillingEmail(),
        ];
    }

    private function getInvoiceData(Invoice $invoice): array
    {
        return [
            'id' => (string) $invoice->getId(),
            'number' => $invoice->getInvoiceNumber(),
            'total' => $invoice->getTotal(),
            'total_display' => (string) $invoice->getTotalMoney(),
            'sub_total' => $invoice->getSubTotal(),
            'vat_total' => $invoice->getVatTotal(),
            'currency' => $invoice->getCurrency(),
            'lines' => array_map([$this, 'getInvoiceLineData'], $invoice->getLines()->toArray()),
            'biller_address' => $this->getAddress($invoice->getBillerAddress()),
            'payee_address' => $this->getAddress($invoice->getPayeeAddress()),
            'created_at' => $invoice->getCreatedAt()->format(\DATE_ATOM),
        ];
    }

    private function getInvoiceLineData(InvoiceLine $invoiceLine): array
    {
        return [
            'total' => $invoiceLine->getTotal(),
            'total_display' => (string) $invoiceLine->getTotalMoney(),
            'sub_total' => $invoiceLine->getSubTotal(),
            'vat_total' => $invoiceLine->getVatTotal(),
            'vat_percentage' => $invoiceLine->getVatPercentage(),
            'description' => $invoiceLine->getDescription(),
        ];
    }

    protected function getBrandData(BrandSettings $brandSettings): array
    {
        return [
            'name' => $brandSettings->getBrandName(),
            'address' => $this->getAddress($brandSettings->getAddress()),
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
}
