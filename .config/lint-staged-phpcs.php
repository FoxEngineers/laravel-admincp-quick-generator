<?php

$header = <<<'EOF'
    This file is part of PHP CS Fixer.
    (c) Fabien Potencier <fabien@symfony.com>
        Dariusz Rumiński <dariusz.ruminski@gmail.com>
    This source file is subject to the MIT license that is bundled
    with this source code in the file LICENSE.
    EOF;

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->exclude('tests/Fixtures')
    ->in(__DIR__)
    ->append([
        __DIR__.'/dev-tools/doc.php',
        // __DIR__.'/php-cs-fixer', disabled, as we want to be able to run bootstrap file even on lower PHP version, to show nice message
    ])
;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        '@PHPUnit100Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ['expectedDeprecation']], // one should use PHPUnit built-in method instead
        'header_comment' => ['header' => ''],
        'heredoc_indentation' => false,
        'modernize_strpos' => true, // needs PHP 8+ or polyfill
        'no_useless_concat_operator' => false,
        'use_arrow_functions' => false,
        'declare_strict_types' => false,
    ])
    ->setFinder($finder)
;

return $config;
