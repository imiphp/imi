# Protobuf

[toc]

## 介绍

Protobuf + HTTP2 = gRPC

protoc 下载和安装：<https://github.com/protocolbuffers/protobuf/releases>

## \Imi\Grpc\Util\ProtobufUtil

### Demo proto 文件定义

```proto
syntax = "proto3";

package grpc;

import "google/protobuf/any.proto";
import "google/protobuf/timestamp.proto";
import "google/protobuf/duration.proto";
import "google/protobuf/struct.proto";
import "google/protobuf/field_mask.proto";

option php_generic_services = true;

enum Test {
    A = 0;
    B = 2;
}

message TestRequest {
    int32 int = 1;
    string string = 2;
    LoginRequest message = 3;
    repeated LoginRequest messages = 4;
    google.protobuf.Any any = 5;
    map<int32, string> map = 6;
    map<string, LoginRequest> map2 = 7;
    repeated google.protobuf.Any anys = 8;
    Test enum = 9;
    bool bool = 10;
    google.protobuf.Timestamp timestamp = 11;
    google.protobuf.Duration duration = 12;
    google.protobuf.Struct struct = 13;
    google.protobuf.FieldMask fieldMask = 14;
    repeated string strings = 15;
}
```

### newMessage

实例化并初始化一个消息

```php
/** @var TestRequest $request */
$request = ProtobufUtil::newMessage(TestRequest::class, [
    'int'      => 1,
]);
```

### newMessageArray

实例化并初始化一个消息列表

```php
/** @var TestRequest[] $requests */
$requests = ProtobufUtil::newMessage(TestRequest::class, [
    [
        'int'      => 1,
    ],
    [
        'int'      => 2,
    ],
]);
```

### setMessageData

向 Grpc Message 对象设置值，每次设置前会清空所有的值

```php
$request = new TestRequest();
$ignoreUnknown = true; // 忽略未知字段
// 下面示例的值代表了几乎所有常见的类型，可供参考
ProtobufUtil::setMessageData($request, [
    'int'      => 1,
    'string'   => 'abc',
    'strings'  => ['a', 'b'],
    'message'  => ['phone' => '114514', 'password' => '123456'],
    'messages' => [
        ['phone' => '1', 'password' => '11'],
        ['phone' => '2', 'password' => '22'],
    ],
    'any'    => [
        '@type'    => 'type.googleapis.com/grpc.LoginRequest',
        'phone'    => '114514',
        'password' => '123',
    ],
    'map' => [
        11 => 'aa',
        22 => 'bb',
    ],
    'map2' => [
        'a' => ['phone' => '1', 'password' => '11'],
        'b' => ['phone' => '2', 'password' => '22'],
    ],
    'anys'    => [
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
        'list1' => [1, 2, 3],
        'list2' => [
            [
                'id'   => 1,
                'name' => 'imi',
            ],
        ],
    ],
    'fieldMask' => 'abc.def',
]
, $ignoreUnknown  // 忽略未知字段，默认可以忽略不传
);
```

### getMessageValue

获取 Grpc Message 对象本身代表的值

```php
$request = new TestRequest();
// 这个 demo 是返回数组
var_dump(ProtobufUtil::getMessageValue($request));

// 指定一些参数设置
var_dump(ProtobufUtil::getMessageValue($request, [
    'enumReturnType' => 'value', // 枚举返回值是值，默认
    'enumReturnType' => 'name', // 枚举返回值是枚举名称
]));
```
