<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 连接.
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @property string|null $poolName          连接池名称
 * @property string      $host              主机
 * @property int         $port              端口
 * @property string      $user              用户名
 * @property string      $password          密码
 * @property string      $vhost
 * @property bool        $insist
 * @property string      $loginMethod
 * @property null        $loginResponse
 * @property string      $locale
 * @property float       $connectionTimeout 连接超时
 * @property float       $readWriteTimeout  读写超时
 * @property null        $context           上下文
 * @property bool        $keepalive
 * @property int         $heartbeat         心跳时间
 * @property float       $channelRpcTimeout 频道 RPC 超时时间
 * @property string|null $sslProtocol       ssl 协议
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Connection extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'poolName';

    /**
     * @param null $loginResponse
     * @param null $context
     */
    public function __construct(?array $__data = null, ?string $poolName = null, string $host = '', int $port = 0, string $user = '', string $password = '', string $vhost = '/', bool $insist = false, string $loginMethod = 'AMQPLAIN', $loginResponse = null, string $locale = 'en_US', float $connectionTimeout = 3.0, float $readWriteTimeout = 3.0, $context = null, bool $keepalive = false, int $heartbeat = 0, float $channelRpcTimeout = 0.0, ?string $sslProtocol = null)
    {
        parent::__construct(...\func_get_args());
    }
}
