<?php

return [
    'Logger'    => [
        // 'coreHandlers'    =>    [],
        'exHandlers'    => [
            [
                'class'        => \Imi\Log\Handler\File::class,
                'options'      => [
                    'levels'         => \Imi\Log\LogLevel::ALL,
                    'fileName'       => dirname(__DIR__, 2) . '/log.log',
                    'format'         => "{Y}-{m}-{d} {H}:{i}:{s} [{level}] {message}\n{trace}",
                    'traceFormat'    => '#{index}  {call} called at [{file}:{line}]',
                    'traceMinimum'   => true,
                ],
            ],
        ],
    ],
];
