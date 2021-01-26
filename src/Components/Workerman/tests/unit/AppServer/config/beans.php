<?php

declare(strict_types=1);

use Imi\Log\LogLevel;

$rootPath = dirname(__DIR__) . '/';

return [
    'Logger'    => [
        'exHandlers'    => [
            // info 级别日志不输出trace
            [
                'class'        => \Imi\Log\Handler\File::class,
                'options'      => [
                    'levels'        => [LogLevel::INFO],
                    'fileName'      => dirname(__DIR__) . '/logs/{Y}-{m}-{d}.log',
                    'format'        => '{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}',
                ],
            ],
            // 指定级别日志输出trace
            [
                'class'        => \Imi\Log\Handler\File::class,
                'options'      => [
                    'levels'        => [
                        LogLevel::ALERT,
                        LogLevel::CRITICAL,
                        LogLevel::DEBUG,
                        LogLevel::EMERGENCY,
                        LogLevel::ERROR,
                        LogLevel::NOTICE,
                        LogLevel::WARNING,
                    ],
                    'fileName'      => dirname(__DIR__) . '/logs/{Y}-{m}-{d}.log',
                    'format'        => "{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}\n{trace}",
                    'traceFormat'   => '#{index}  {call} called at [{file}:{line}]',
                ],
            ],
        ],
    ],
    // 启用超全局变量
    'SuperGlobals'  => [
        'enable'    => true,
    ],
];
