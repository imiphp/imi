<?php

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
                        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImEiOiIxIiwiYiI6IjIifQ.eyJhdWQiOiJhdWRpZW5jZV9hIiwic3ViIjoic3ViamVjdF9hIiwiZXhwIjoxODkzMjkxNTE1LCJpc3MiOiJpc3N1ZXJfYSIsIm5iZiI6MCwianRpIjoiIiwiaWF0IjoxNTc3OTMxNTE1LCJkYXRhIjp7Im1lbWJlcklkIjoxOTI2MDgxN319.-tXlyj1BcVD8GJIE2nQdTPULVpZFD0h5BIQdx_X943E';
                    },
                    'privateKey'    => '123456',
                    'publicKey'     => '123456',
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
                    'privateKey'    => '123456',
                    'publicKey'     => '123456',
                ],
            ],
        ],
    ],
];
