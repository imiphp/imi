<?php

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 连接.
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class Connection extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'poolName';

    /**
     * 连接池名称.
     *
     * @var string|null
     */
    public $poolName;

    /**
     * 主机.
     *
     * @var string
     */
    public $host;

    /**
     * 端口.
     *
     * @var int
     */
    public $port;

    /**
     * 用户名.
     *
     * @var string
     */
    public $user;

    /**
     * 密码
     *
     * @var string
     */
    public $password;

    /**
     * vhost.
     *
     * @var string
     */
    public $vhost = '/';

    /**
     * insist.
     *
     * @var bool
     */
    public $insist = false;

    /**
     * loginMethod.
     *
     * @var string
     */
    public $loginMethod = 'AMQPLAIN';

    /**
     * loginResponse.
     *
     * @deprecated
     *
     * @var null
     */
    public $loginResponse = null;

    /**
     * locale.
     *
     * @var string
     */
    public $locale = 'en_US';

    /**
     * 连接超时.
     *
     * @var float
     */
    public $connectionTimeout = 3.0;

    /**
     * 读写超时.
     *
     * @var float
     */
    public $readWriteTimeout = 3.0;

    /**
     * 上下文.
     *
     * @var null
     */
    public $context = null;

    /**
     * keepalive.
     *
     * @var bool
     */
    public $keepalive = false;

    /**
     * 心跳时间.
     *
     * @var int
     */
    public $heartbeat = 0;

    /**
     * 频道 RPC 超时时间.
     *
     * @var float
     */
    public $channelRpcTimeout = 0.0;

    /**
     * ssl 协议.
     *
     * @var string|null
     */
    public $sslProtocol = null;
}
