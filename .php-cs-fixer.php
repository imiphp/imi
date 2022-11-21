<?php

declare(strict_types=1);

if (!file_exists(__DIR__ . '/src'))
{
    exit(0);
}

return (new PhpCsFixer\Config())
    ->setRules([
        '@PHP74Migration'            => true,
        '@PHP74Migration:risky'      => true,
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
        'fopen_flags'                  => false,
        'protected_to_private'         => false,
        'native_function_invocation'   => true,
        'native_constant_invocation'   => true,
        'combine_nested_dirname'       => true,
        'single_quote'                 => true,
        'single_space_after_construct' => [
            'constructs' => ['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'throw', 'trait', 'try', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from'],
        ],
        'braces'                     => [
            'position_after_control_structures' => 'next',
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
        'void_return'                      => false, // BUG:https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/6690
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->notName('*.macro.php')
            ->notPath('src/Components/swoole/src/Util/Coroutine.typed.php') // 兼容 Swoole 5.0，需要 PHP >= 8.0
            ->notPath('src/Components/hprose/src/Imi-Server-Hprose/Server.php') // bug: https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/6534
            ->notPath('src/Components/grpc/example/grpc')
    )
;
