<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'ordered_imports' => true,
        'ordered_attributes' => true,
        'ordered_class_elements' => true,
        'ordered_types' => true,
        'no_unused_imports' => true,
        'yoda_style' => true,
        'elseif' => true,
        'header_comment' => ['header' => 'Copyright Iain Cambridge 2023-2025.
                
Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.'],
    ])
    ->setFinder($finder)
    ->setParallelConfig(ParallelConfigFactory::detect())
;
