<?php

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace App\Pdf;

use App\Entity\Customer;
use App\Repository\TemplateRepositoryInterface;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Pdf\GeneratorInterface;
use Twig\Environment;

class ReceiptPdfGenerator
{
    public function __construct(
        private TemplateRepositoryInterface $templateRepository,
        private Environment $twig,
        private GeneratorInterface $pdfGenerator,
    ) {
    }

    public function generate(Receipt $receipt)
    {
        try {
            $template = $this->templateRepository->getByNameAndBrand('receipt', $receipt->getCustomer()->getGroup());
        } catch (NoEntityFoundException $exception) {
            $template = $this->templateRepository->getByNameAndBrand('receipt', Customer::DEFAULT_GROUP);
        }

        $twigTemplate = $this->twig->createTemplate($template->getContent());
        $content = $this->twig->render($twigTemplate, ['receipt' => $receipt, 'customer' => $receipt->getCustomer()]);

        return $this->pdfGenerator->generate($content);
    }
}
