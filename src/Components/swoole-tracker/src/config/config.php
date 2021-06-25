<?php

declare(strict_types=1);

return [
    'beanScan'  => [
        'Imi\SwooleTracker\Http\Middleware',
        'Imi\SwooleTracker\WebSocket\Middleware',
        'Imi\SwooleTracker\TCP\Middleware',
        'Imi\SwooleTracker\UDP\Middleware',
    ],
];
