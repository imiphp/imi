<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    // 'beanScan'    => [
    //     'Imi\JWT\Test\Test',
    // ],
    'components'    => [
        // 引入本组件
        'jwt'    => 'Imi\JWT',
    ],
    'ignoreNamespace'   => [
    ],
    'beans'    => [
        'JWT'   => [
            'list'  => [
                'a' => [
                    'audience'  => 'audience_a',
                    'subject'   => 'subject_a',
                    'expires'   => 86400,
                    'issuer'    => 'issuer_a',
                    'headers'   => [
                        'a' => '1',
                        'b' => '2',
                    ],
                    'tokenHandler'  => function () {
                        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImEiOiIxIiwiYiI6IjIifQ.eyJhdWQiOiJhdWRpZW5jZV9hIiwic3ViIjoic3ViamVjdF9hIiwiZXhwIjo0MTE3MTkwNDAwLCJpc3MiOiJpc3N1ZXJfYSIsIm5iZiI6MTY2MDk2MzI3OC4zMDc3NzEsImp0aSI6IiIsImlhdCI6MTY2MDk2MzI3OC4zMDc3NzEsImRhdGEiOnsibWVtYmVySWQiOjE5MjYwODE3fX0.mtXSMJrvX15-Gn0h8cPXfwtnVdyJdV8eGqwD5qRqGYc';
                    },
                    'privateKey'    => '1010e4c5907b331741eeacae59ec6f69',
                    'publicKey'     => '1010e4c5907b331741eeacae59ec6f69',
                ],
                'b' => [
                    'audience'  => 'audience_a',
                    'subject'   => 'subject_a',
                    'expires'   => 86400,
                    'issuer'    => 'issuer_a',
                    'headers'   => [
                        'a' => '1',
                        'b' => '2',
                    ],
                    'tokenHandler'  => function () {
                        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImEiOiIxIiwiYiI6IjIifQ.eyJhdWQiOiJhdWRpZW5jZV9hIiwic3ViIjoic3ViamVjdF9hIiwiZXhwIjotMTM2ODg2NDAwMCwiaXNzIjoiaXNzdWVyX2EiLCJuYmYiOjAsImp0aSI6IiIsImlhdCI6MTU4MTQxMjIwMywiZGF0YSI6eyJtZW1iZXJJZCI6MTkyNjA4MTd9fQ.PAZxqO48qzu_4JBK5jyRK2nJlDNHiLkBqKQ6QLb-Duo';
                    },
                    'privateKey'    => '1010e4c5907b331741eeacae59ec6f69',
                    'publicKey'     => '1010e4c5907b331741eeacae59ec6f69',
                ],
            ],
        ],
    ],
];
