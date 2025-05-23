<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Invoice\Formatter;

use BillaBear\Entity\BrandSettings;
use BillaBear\Entity\Customer;
use BillaBear\Entity\Invoice;
use BillaBear\Entity\InvoiceLine;
use BillaBear\Entity\Template;
use BillaBear\Repository\TemplateRepositoryInterface;
use Parthenon\Common\Address;
use Parthenon\Common\Pdf\GeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

class InvoicePdfGenerator implements InvoiceFormatterInterface
{
    public const FORMAT_NAME = 'app.invoices.delivery.format.pdf';

    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        #[Autowire('@template_twig')]
        private Environment $templateTwig,
        private GeneratorInterface $pdfGenerator,
    ) {
    }

    public function generate(Invoice $invoice): mixed
    {
        $customer = $invoice->getCustomer();

        if (!$customer instanceof Customer) {
            throw new \LogicException('Invalid customer type');
        }

        $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_INVOICE, $customer->getLocale(), $customer->getBrand());

        if (!$template) {
            $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_INVOICE, $customer->getLocale(), Customer::DEFAULT_BRAND);
        }

        if (!$template) {
            $template = $this->templateRepository->getByNameAndLocaleAndBrand(Template::NAME_INVOICE, Customer::DEFAULT_LOCALE, Customer::DEFAULT_BRAND);
        }

        if (!$template) {
            throw new \Exception('Unable to find pdf template');
        }

        $twigTemplate = $this->templateTwig->createTemplate($template->getContent());
        $content = $this->templateTwig->render($twigTemplate, $this->getData($invoice));

        return $this->pdfGenerator->generate($content);
    }

    public function filename(Invoice $invoice): string
    {
        return sprintf('invoice-%s.pdf', $invoice->getInvoiceNumber());
    }

    public function supports(string $type): bool
    {
        $parts = explode('.', $type);

        return self::FORMAT_NAME === $type || strtolower(end($parts)) === $type;
    }

    public function name(): string
    {
        return self::FORMAT_NAME;
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

    private function getData(Invoice $invoice): array
    {
        return [
            'customer' => $this->getCustomerData($invoice->getCustomer()),
            'brand' => $this->getBrandData($invoice->getCustomer()->getBrandSettings()),
            'invoice' => $this->getInvoiceData($invoice),
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
            'tax_total' => $invoice->getTaxTotal(),
            'currency' => $invoice->getCurrency(),
            'lines' => array_map([$this, 'getInvoiceLineData'], $invoice->getLines()->toArray()),
            'biller_address' => $this->getAddress($invoice->getBillerAddress()),
            'payee_address' => $this->getAddress($invoice->getPayeeAddress()),
            'created_at' => $invoice->getCreatedAt()->format(\DATE_ATOM),
            'due_date' => $invoice->getDueAt()?->format(\DATE_ATOM),
        ];
    }

    private function getInvoiceLineData(InvoiceLine $invoiceLine): array
    {
        return [
            'total' => $invoiceLine->getTotal(),
            'total_display' => (string) $invoiceLine->getTotalMoney(),
            'sub_total' => $invoiceLine->getSubTotal(),
            'tax_total' => $invoiceLine->getTaxTotal(),
            'tax_percentage' => $invoiceLine->getTaxPercentage(),
            'description' => $invoiceLine->getDescription(),
            'tax_type' => $invoiceLine->getTaxType()?->getName(),
            'tax_country' => $invoiceLine->getTaxCountry(),
            'metadata' => $invoiceLine->getMetadata(),
        ];
    }
}
