<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2027 ( 3 years after 2024.1 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Pdf;

use App\Entity\BrandSettings;
use App\Entity\Customer;
use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Entity\Template;
use App\Repository\TemplateRepositoryInterface;
use Parthenon\Common\Address;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Pdf\GeneratorInterface;
use Twig\Environment;

class QuotePdfGenerator
{
    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        private Environment $twig,
        private GeneratorInterface $pdfGenerator,
    ) {
    }

    public function generate(Quote $quote)
    {
        $customer = $quote->getCustomer();

        if (!$customer instanceof Customer) {
            throw new \LogicException('Invalid customer type');
        }
        try {
            $template = $this->templateRepository->getByNameAndBrand(Template::NAME_QUOTE, $customer->getBrand());
        } catch (NoEntityFoundException $exception) {
            $template = $this->templateRepository->getByNameAndBrand(Template::NAME_QUOTE, Customer::DEFAULT_BRAND);
        }

        $twigTemplate = $this->twig->createTemplate($template->getContent());
        $content = $this->twig->render($twigTemplate, $this->getData($quote));

        return $this->pdfGenerator->generate($content);
    }

    private function getData(Quote $invoice): array
    {
        return [
            'customer' => $this->getCustomerData($invoice->getCustomer()),
            'brand' => $this->getBrandData($invoice->getCustomer()->getBrandSettings()),
            'quote' => $this->getInvoiceData($invoice),
        ];
    }

    protected function getCustomerData(Customer $customer): array
    {
        return [
            'name' => $customer->getName(),
            'email' => $customer->getBillingEmail(),
        ];
    }

    private function getInvoiceData(Quote $invoice): array
    {
        return [
            'id' => (string) $invoice->getId(),
            'total' => $invoice->getTotal(),
            'total_display' => (string) $invoice->getTotalMoney(),
            'sub_total' => $invoice->getSubTotal(),
            'tax_total' => $invoice->getTaxTotal(),
            'currency' => $invoice->getCurrency(),
            'lines' => array_map([$this, 'getQuoteLineData'], $invoice->getLines()->toArray()),
            'created_at' => $invoice->getCreatedAt()->format(\DATE_ATOM),
        ];
    }

    private function getQuoteLineData(QuoteLine $quoteLine): array
    {
        return [
            'total' => $quoteLine->getTotal(),
            'total_display' => (string) $quoteLine->getTotalMoney(),
            'sub_total' => $quoteLine->getSubTotal(),
            'tax_total' => $quoteLine->getTaxTotal(),
            'tax_percentage' => $quoteLine->getTaxPercentage(),
            'description' => $quoteLine->getDescription(),
            'tax_type' => $quoteLine->getTaxType()->value,
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
}
