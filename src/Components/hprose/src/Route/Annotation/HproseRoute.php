<?php

declare(strict_types=1);

namespace Imi\Hprose\Route\Annotation;

use Imi\Bean\Annotation\Parser;
use Imi\Rpc\Route\Annotation\RpcRoute;

/**
 * Hprose 路由注解.
 *
 * @Annotation
 *
 * @Target("METHOD")
 * @Parser("Imi\Rpc\Route\Annotation\Parser\RpcControllerParser")
 *
 * @property string $rpcType     RPC 协议类型；继承本类后必须赋值
 * @property int    $mode        该设置表示该服务函数返回的结果类型，它有4个取值，分别是：Hprose\ResultMode::Normal 是默认值，表示返回正常的已被反序列化的结果。Hprose\ResultMode::Serialized 表示返回的结果保持序列化的格式。Hprose\ResultMode::Raw 表示返回原始数据。Hprose\ResultMode::RawWithEndTag 表示返回带有结束标记的原始数据。
 * @property bool   $simple      该设置表示本服务函数所返回的结果是否为简单数据。默认值为 false
 * @property bool   $oneway      该设置表示本服务函数是否不需要等待返回值。当该设置为 true 时，调用会异步开始，并且不等待结果，立即返回 null 给客户端。默认值为 false
 * @property bool   $async       该设置表示本服务函数是否为异步函数，异步函数的最后一个参数是一个回调函数，用户需要在异步函数中调用该回调方法来传回返回值
 * @property bool   $passContext 该属性为 boolean 类型，默认值为 false。该属性表示在调用中是否将 $context 自动作为最后一个参数传入调用方法。你也可以针对某个服务函数/方法进行单独设置。除非所有的服务方法的参数最后都定义了 $context 参数。否则，建议不要修改默认设置，而是针对某个服务函数/方法进行单独设置。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class HproseRoute extends RpcRoute
{
    public function __construct(?array $__data = null, string $rpcType = 'Hprose', int $mode = \Hprose\ResultMode::Normal, bool $simple = false, bool $oneway = false, bool $async = false, bool $passContext = false)
    {
        parent::__construct(...\func_get_args());
    }
}
