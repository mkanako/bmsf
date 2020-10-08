<?php

$conf = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_useless_return' => true,
        'array_indentation' => true,
        'blank_line_before_statement' => false,
        'method_chaining_indentation' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'no_superfluous_phpdoc_tags' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__ . '/src')
    );

if (file_exists('/Volumes/ramdisk')) {
    $conf->setCacheFile('/Volumes/ramdisk/.php_cs.cache');
}

return $conf;
