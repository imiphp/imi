<?php

declare(strict_types=1);

if (!file_exists(__DIR__ . '/src'))
{
    exit(0);
}

return (new PhpCsFixer\Config())
    ->setRules([
        '@PHP81Migration'            => true,
        '@PHP80Migration:risky'      => true,
        '@Symfony'                   => true,
        '@Symfony:risky'             => true,
        '@DoctrineAnnotation'        => true,
        'php_unit_dedicate_assert'   => ['target' => '5.6'],
        'array_syntax'               => ['syntax' => 'short'],
        'array_indentation'          => true,
        'binary_operator_spaces'     => [
            'operators' => [
                '=>' => 'align_single_space',
            ],
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'fopen_flags'                   => false,
        'protected_to_private'          => false,
        'native_function_invocation'    => true,
        'native_constant_invocation'    => true,
        'combine_nested_dirname'        => true,
        'single_quote'                  => true,
        'single_space_around_construct' => [
            'constructs_followed_by_a_single_space' => [
                'abstract',
                'as',
                'attribute',
                'break',
                'case',
                'catch',
                'class',
                'clone',
                'comment',
                'const',
                'const_import',
                'continue',
                'do',
                'echo',
                'else',
                'elseif',
                'enum',
                'extends',
                'final',
                'finally',
                'for',
                'foreach',
                'function',
                'function_import',
                'global',
                'goto',
                'if',
                'implements',
                'include',
                'include_once',
                'instanceof',
                'insteadof',
                'interface',
                'match',
                'named_argument',
                // 'namespace', // 兼容性移除
                'new',
                'open_tag_with_echo',
                'php_doc',
                'php_open',
                'print',
                'private',
                'protected',
                'public',
                'readonly',
                'require',
                'require_once',
                'return',
                'static',
                'switch',
                'throw',
                'trait',
                'try',
                'type_colon',
                'use',
                'use_lambda',
                'use_trait',
                'var',
                'while',
                'yield',
                'yield_from',
            ],
        ],
        'control_structure_continuation_position' => [
            'position' => 'next_line',
        ],
        'single_line_comment_style'  => false,
        'phpdoc_to_comment'          => false,
        'declare_strict_types'       => true,
        'heredoc_indentation'        => [
            'indentation' => 'same_as_start',
        ],
        'no_trailing_whitespace_in_string' => false,
        'static_lambda'                    => true,
        'modernize_strpos'                 => true,
        'regular_callable_call'            => true,
        'combine_consecutive_issets'       => true,
        'combine_consecutive_unsets'       => true,
        'explicit_indirect_variable'       => true,
        'no_useless_return'                => true,
        'return_assignment'                => true,
        'explicit_string_variable'         => true,
        'heredoc_to_nowdoc'                => true,
        // Symfony 冲突
        'braces_position'                  => [
            'control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end',
        ],
        'nullable_type_declaration_for_default_null_value' => false,
        'no_superfluous_phpdoc_tags'                       => [
            'allow_mixed'         => true,
            'allow_unused_params' => false,
        ],
        'no_null_property_initialization'  => false,
        'phpdoc_to_param_type'             => [
            'scalar_types' => true,
        ],
        'phpdoc_to_return_type' => [
            'scalar_types' => true,
        ],
        'phpdoc_to_property_type' => [
            'scalar_types' => true,
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude('src/Components/grpc/example/grpc')
            ->exclude('src/Components/model/tests/Model/Base')
            ->exclude('src/Components/pgsql/tests/Model/Base')
    )
;
