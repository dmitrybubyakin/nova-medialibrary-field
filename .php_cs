<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->notPath('vendor')
    ->notPath('bootstrap')
    ->notPath('storage')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,

        'array_syntax' => ['syntax' => 'short'],
        'array_indentation' => true,
        'trailing_comma_in_multiline_array' => true,

        'ordered_imports' => ['sortAlgorithm' => 'alpha'],
        'no_unused_imports' => true,

        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'cast_spaces' => true,
        'not_operator_with_successor_space' => true,

        'phpdoc_trim' => true,
        'phpdoc_scalar' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,

        'no_empty_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => true,

        'class_attributes_separation' => [
            'elements' => [
                'method',
            ],
        ],

        'visibility_required' => ['property', 'method'],

        'void_return' => true,
        'protected_to_private' => true,

        'explicit_string_variable' => true,
        'single_quote' => true,

        'declare_strict_types' => true,
    ])
    ->setFinder($finder);
