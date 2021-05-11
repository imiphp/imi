<?php

return [
    'beanScan'  => [
        'Imi\AMQP\Annotation',
        'Imi\AMQP\Pool',
        'Imi\AMQP\Queue',
    ],
    // 组件命名空间
    'components'    => [
        'Queue'  => 'Imi\Queue',
    ],
];
