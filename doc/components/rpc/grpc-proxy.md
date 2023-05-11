# gRPC 的 HTTP 代理网关

[toc]

## 介绍

imi v2.1.22 新加入的 gRPC HTTP 代理网关，作用是可以用 HTTP + JSON 的方式请求接口，而不用 Protobuf + HTTP2 来直接调用 gRPC。

其实这里可以画一个很厉害的架构图，但对使用没有什么帮助，就不放了。

## 使用

### proto 文件生成 PHP 代码

这一步和 [gRPC 服务开发](/v2.1/components/rpc/grpc.html#%E6%9C%8D%E5%8A%A1%E5%92%8C%E6%B6%88%E6%81%AF%E6%A0%BC%E5%BC%8F%E5%AE%9A%E4%B9%89)一样，就不再赘述。

### 配置 gRPC 连接池

因为我们服务端是代理网关，其实就是作为客户端去连服务端，所以要配置连接池。

可参考 [gRPC 服务开发](/v2.1/components/rpc/grpc.html#%E5%AE%A2%E6%88%B7%E7%AB%AF)。

### 绑定 gRPC 接口（非必须）

如果你的 gRPC 接口是跟代理同一个服务，那么 imi 会帮你做好绑定，不需要做这一步。

如果你是代理外部服务的 gRPC 接口，此步骤是必须的。

在 `@app.beans` 中配置：

```php
[
    'GrpcInterfaceManager' => [
        // 绑定的服务接口
        'binds' => [
            \Grpc\AuthServiceInterface::class, // 这里换成你自己的
        ],
    ],
]
```

### 编写代理接口

就是写一个 HTTP 接口，没什么多说的，直接上代码：

```php
<?php

declare(strict_types=1);

namespace GrpcApp\GrpcServer\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\Controller\HttpController;
use Imi\Grpc\Proxy\Http\GrpcHttpProxy;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @Controller("/proxy/")
 */
class ProxyController extends HttpController
{
    /**
     * @Inject("GrpcHttpProxy")
     */
    protected GrpcHttpProxy $grpcHttpProxy;

    /**
     * @Action
     * @Route("grpc/{service}/{method}")
     *
     * @return mixed
     */
    public function proxy(string $service, string $method)
    {
        // 这里的 grpc 是连接池名称，换成你自己的
        return $this->grpcHttpProxy->proxy('grpc', $this->request, $this->response, $service, $method);
    }
}
```

> `@Controller("/proxy/")` 和 `@Route("grpc/{service}/{method}")` 的路由都是可以自己定义的，这里仅仅作为演示用。

### 测试

这里拿 gRPC 服务开发中的示例来测试：

```shell
curl --location --request POST -X POST "http://127.0.0.1:8080/proxy/grpc/grpc.AuthService/login" \
--header 'Content-Type: application/json' \
--data '{
    "phone": "12345678901",
    "password": "123456"
}'
```

返回：

```js
{
	"success": true,
	"error": ""
}
```

### 注意事项

* 如何传请求参数

请求参数可以是 GET、POST、JSON 数据，只要 imi 能正常接收就可以。

推荐使用 JSON，可以支持复杂数据结构。

* 请求参数格式

下面给个示例，可以参考，几乎覆盖了绝大部分常见类型

proto:

```proto
syntax = "proto3";

package grpc;

import "google/protobuf/any.proto";
import "google/protobuf/timestamp.proto";
import "google/protobuf/duration.proto";
import "google/protobuf/struct.proto";
import "google/protobuf/field_mask.proto";

option php_generic_services = true;

service AuthService {
    rpc Login (LoginRequest) returns (LoginResponse);
}

message LoginRequest {
    string phone = 1;       // 手机号
    string password = 2;    // 密码
}

message LoginResponse {
    bool success = 1;       // 是否成功
    string error = 2;       // 错误信息
}

service TestService {
    rpc Test (TestRequest) returns (TestRequest);
}

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

JSON 请求参数：

```js
{
    "int": 1,
    "string": "abc",
    "strings": [
        "a",
        "b"
    ],
    "message": {
        "phone": "114514",
        "password": "123456"
    },
    "messages": [
        {
            "phone": "1",
            "password": "11"
        },
        {
            "phone": "2",
            "password": "22"
        }
    ],
    "any": {
        "@type": "type.googleapis.com\/grpc.LoginRequest",
        "phone": "114514",
        "password": "123"
    },
    "map": {
        "11": "aa",
        "22": "bb"
    },
    "map2": {
        "a": {
            "phone": "1",
            "password": "11"
        },
        "b": {
            "phone": "2",
            "password": "22"
        }
    },
    "anys": [
        {
            "@type": "type.googleapis.com\/grpc.LoginRequest",
            "phone": "114514",
            "password": "123"
        }
    ],
    "enum": 2,
    "bool": true,
    "timestamp": "2018-06-21T04:00:00Z",
    "duration": "1s",
    "struct": {
        "null": null,
        "number": 3.14,
        "string": "abc",
        "bool": true,
        "struct": {
            "id": 1,
            "name": "imi"
        },
        "list1": [
            1,
            2,
            3
        ],
        "list2": [
            {
                "id": 1,
                "name": "imi"
            }
        ]
    },
    "fieldMask": "abc.def"
}
```
