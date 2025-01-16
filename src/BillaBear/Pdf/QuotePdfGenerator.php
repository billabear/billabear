<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pdf;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Quote;
use BillaBear\Entity\QuoteLine;
use BillaBear\Entity\Template;
use BillaBear\Repository\TemplateRepositoryInterface;
use Parthenon\Common\Address;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Common\Pdf\GeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

class QuotePdfGenerator
{
    use LoggerAwareTrait;

    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        #[Autowire('@template_twig')]
        private Environment $templateTwig,
        private GeneratorInterface $pdfGenerator,
    ) {
    }

    public function generate(Quote $quote)
    {
        $customer = $quote->getCustomer();

        if (!$customer instanceof Customer) {
            throw new \LogicException('Invalid customer type');
        }
        $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_QUOTE, $customer->getLocale(), $customer->getBrand());

        if (!$template) {
            $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_QUOTE, $customer->getLocale(), Customer::DEFAULT_BRAND);
        }

        if (!$template) {
            $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_QUOTE, Customer::DEFAULT_LOCALE, Customer::DEFAULT_BRAND);
        }

        if (!$template) {
            $this->getLogger()->critical('Quote Template not found');

            throw new \Exception('Unable to find pdf template');
        }

        $twigTemplate = $this->templateTwig->createTemplate($template->getContent());
        $content = $this->templateTwig->render($twigTemplate, $this->getData($quote));

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

    private function getData(Quote $invoice): array
    {
        return [
            'customer' => $this->getCustomerData($invoice->getCustomer()),
            'brand' => $this->getBrandData($invoice->getCustomer()->getBrandSettings()),
            'quote' => $this->getInvoiceData($invoice),
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
            'tax_type' => $quoteLine->getTaxType()->getName(),
        ];
    }
}
