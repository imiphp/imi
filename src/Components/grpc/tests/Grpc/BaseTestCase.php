<?php

declare(strict_types=1);

namespace Imi\Grpc\Test;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    public const DATA = [
        'int'       => 1,
        'string'    => 'abc',
        'strings'   => ['a', 'b'],
        'message'   => ['phone' => '114514', 'password' => '123456'],
        'messages'  => [
            ['phone' => '1', 'password' => '11'],
            ['phone' => '2', 'password' => '22'],
        ],
        'any'       => [
            '@type'    => 'type.googleapis.com/grpc.LoginRequest',
            'phone'    => '114514',
            'password' => '123',
        ],
        'map'       => [
            11 => 'aa',
            22 => 'bb',
        ],
        'map2'      => [
            'a' => ['phone' => '1', 'password' => '11'],
            'b' => ['phone' => '2', 'password' => '22'],
        ],
        'anys'      => [
            [
                '@type'    => 'type.googleapis.com/grpc.LoginRequest',
                'phone'    => '114514',
                'password' => '123',
            ],
        ],
        'enum'      => 2,
        'bool'      => true,
        'timestamp' => '2018-06-21T04:00:00Z',
        'duration'  => '1s',
        'struct'    => [
            'null'   => null,
            'number' => 3.14,
            'string' => 'abc',
            'bool'   => true,
            'struct' => [
                'id'   => 1,
                'name' => 'imi',
            ],
            'list1'  => [1, 2, 3],
            'list2'  => [
                [
                    'id'   => 1,
                    'name' => 'imi',
                ],
            ],
        ],
        'fieldMask' => 'abc.def',
    ];
}
