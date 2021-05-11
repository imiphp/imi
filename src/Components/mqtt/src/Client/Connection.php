<?php

namespace Imi\MQTT\Client;

use BinSoul\Net\Mqtt\DefaultConnection;
use BinSoul\Net\Mqtt\Message;

/**
 * MQTT 连接信息.
 */
class Connection extends DefaultConnection
{
    /**
     * 主机地址
     *
     * @var string
     */
    private $host;

    /**
     * 端口号.
     *
     * @var int
     */
    private $port;

    /**
     * 超时时间，单位：秒.
     *
     * @var float|null
     */
    private $timeout;

    /**
     * Ping 时间间隔，为 NULL 则不自动 Ping.
     *
     * @var float|null
     */
    private $pingTimespan;

    /**
     * 使用 SSL 连接.
     *
     * @var bool
     */
    private $ssl = false;

    /**
     * 证书文件.
     *
     * @var string|null
     */
    private $sslCertFile;

    /**
     * 证书密钥文件.
     *
     * @var string|null
     */
    private $sslKeyFile;

    /**
     * 验证服务器端证书。
     *
     * @var bool
     */
    private $sslVerifyPeer = true;

    /**
     * 允许自签名证书.
     *
     * @var bool
     */
    private $sslAllowSelfSigned = false;

    /**
     * 服务器主机名称.
     *
     * @var string|null
     */
    private $sslHostName;

    /**
     * CA 证书.
     *
     * @var string|null
     */
    private $sslCafile;

    /**
     * 证书目录.
     *
     * @var string|null
     */
    private $sslCapath;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(
        string $host,
        int $port,
        ?float $timeout = null,
        ?float $pingTimespan = null,
        string $username = '',
        string $password = '',
        Message $will = null,
        string $clientID = '',
        int $keepAlive = 60,
        int $protocol = 4,
        bool $clean = true
    ) {
        parent::__construct($username, $password, $will, $clientID, $keepAlive, $protocol, $clean);
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->pingTimespan = $pingTimespan;
    }

    /**
     * Get 主机地址
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * With 主机地址
     *
     * @param string $host 主机地址
     *
     * @return self
     */
    public function withHost(string $host)
    {
        $result = clone $this;
        $result->host = $host;

        return $result;
    }

    /**
     * Get 端口号.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * With 端口号.
     *
     * @param int $port 端口号
     *
     * @return self
     */
    public function withPort(int $port)
    {
        $result = clone $this;
        $result->port = $port;

        return $result;
    }

    /**
     * Get 超时时间，单位：秒.
     *
     * @return float|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * With 超时时间，单位：秒.
     *
     * @param float $timeout 超时时间，单位：秒
     *
     * @return self
     */
    public function withTimeout(?float $timeout)
    {
        $result = clone $this;
        $result->timeout = $timeout;

        return $result;
    }

    /**
     * Get ping 时间间隔，为 NULL 则不自动 Ping.
     *
     * @return float|null
     */
    public function getPingTimespan()
    {
        return $this->pingTimespan;
    }

    /**
     * With ping 时间间隔，为 NULL 则不自动 Ping.
     *
     * @param float|null $pingTimespan Ping 时间间隔，为 NULL 则不自动 Ping
     *
     * @return self
     */
    public function withPingTimespan($pingTimespan)
    {
        $result = clone $this;
        $result->pingTimespan = $pingTimespan;

        return $result;
    }

    /**
     * Get 使用 SSL 连接.
     *
     * @return bool
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * With 使用 SSL 连接.
     *
     * @param bool $ssl 使用 SSL 连接
     *
     * @return self
     */
    public function withSsl(bool $ssl)
    {
        $result = clone $this;
        $result->ssl = $ssl;

        return $result;
    }

    /**
     * Get 证书文件.
     *
     * @return string|null
     */
    public function getSslCertFile()
    {
        return $this->sslCertFile;
    }

    /**
     * With 证书文件.
     *
     * @param string|null $sslCertFile 证书文件
     *
     * @return self
     */
    public function withSslCertFile($sslCertFile)
    {
        $result = clone $this;
        $result->sslCertFile = $sslCertFile;

        return $result;
    }

    /**
     * Get 证书密钥文件.
     *
     * @return string|null
     */
    public function getSslKeyFile()
    {
        return $this->sslKeyFile;
    }

    /**
     * With 证书密钥文件.
     *
     * @param string|null $sslKeyFile 证书密钥文件
     *
     * @return self
     */
    public function withSslKeyFile($sslKeyFile)
    {
        $result = clone $this;
        $result->sslKeyFile = $sslKeyFile;

        return $result;
    }

    /**
     * Get 验证服务器端证书。
     *
     * @return bool
     */
    public function getSslVerifyPeer()
    {
        return $this->sslVerifyPeer;
    }

    /**
     * With 验证服务器端证书。
     *
     * @param bool $sslVerifyPeer 验证服务器端证书
     *
     * @return self
     */
    public function withSslVerifyPeer(bool $sslVerifyPeer)
    {
        $result = clone $this;
        $result->sslVerifyPeer = $sslVerifyPeer;

        return $result;
    }

    /**
     * Get 允许自签名证书.
     *
     * @return bool
     */
    public function getSslAllowSelfSigned()
    {
        return $this->sslAllowSelfSigned;
    }

    /**
     * With 允许自签名证书.
     *
     * @param bool $sslAllowSelfSigned 允许自签名证书
     *
     * @return self
     */
    public function withSslAllowSelfSigned(bool $sslAllowSelfSigned)
    {
        $result = clone $this;
        $result->sslAllowSelfSigned = $sslAllowSelfSigned;

        return $result;
    }

    /**
     * Get 服务器主机名称.
     *
     * @return string|null
     */
    public function getSslHostName()
    {
        return $this->sslHostName;
    }

    /**
     * With 服务器主机名称.
     *
     * @param string|null $sslHostName 服务器主机名称
     *
     * @return self
     */
    public function withSslHostName($sslHostName)
    {
        $result = clone $this;
        $result->sslHostName = $sslHostName;

        return $result;
    }

    /**
     * Get cA 证书.
     *
     * @return string|null
     */
    public function getSslCafile()
    {
        return $this->sslCafile;
    }

    /**
     * With cA 证书.
     *
     * @param string|null $sslCafile CA 证书
     *
     * @return self
     */
    public function withSslCafile($sslCafile)
    {
        $result = clone $this;
        $result->sslCafile = $sslCafile;

        return $result;
    }

    /**
     * Get 证书目录.
     *
     * @return string|null
     */
    public function getSslCapath()
    {
        return $this->sslCapath;
    }

    /**
     * With 证书目录.
     *
     * @param string|null $sslCapath 证书目录
     *
     * @return self
     */
    public function withSslCapath($sslCapath)
    {
        $result = clone $this;
        $result->sslCapath = $sslCapath;

        return $result;
    }
}
