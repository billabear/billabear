<?php

/*
 * Copyright Humbly Arrogant Software Limited 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Pdf;

use BillaBear\Repository\SettingsRepositoryInterface;
use DocRaptor\Configuration;
use DocRaptor\DocApi;
use Mpdf\Mpdf;
use Parthenon\Common\Pdf\DocRaptorGenerator;
use Parthenon\Common\Pdf\GeneratorInterface;
use Parthenon\Common\Pdf\MpdfGenerator;
use Parthenon\Common\Pdf\SnappyGenerator;

class PdfGeneratorFactory
{
    public function __construct(private SettingsRepositoryInterface $settingsRepository)
    {
    }

    public function create(): GeneratorInterface
    {
        $settings = $this->settingsRepository->getDefaultSettings();

        if (PdfGeneratorType::WKHTMLTOPDF === $settings->getSystemSettings()->getPdfGenerator()) {
            return new SnappyGenerator($settings->getSystemSettings()->getPdfBin() ?? '/usr/bin/wkhtmltopdf');
        }

        if (PdfGeneratorType::DOCRAPTOR === $settings->getSystemSettings()->getPdfGenerator()) {
            $config = new Configuration();
            $config->setUsername($settings->getSystemSettings()->getPdfApiKey());
            $docApi = new DocApi(config: $config);

            return new DocRaptorGenerator($docApi);
        }

        return new MpdfGenerator(new Mpdf(['tempDir' => $settings->getSystemSettings()->getPdfTmpDir() ?? '/tmp']));
    }
}
