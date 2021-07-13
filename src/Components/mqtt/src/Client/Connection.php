<?php

declare(strict_types=1);

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
     */
    private string $host;

    /**
     * 端口号.
     */
    private int $port;

    /**
     * 超时时间，单位：秒.
     */
    private ?float $timeout;

    /**
     * Ping 时间间隔，为 NULL 则不自动 Ping.
     */
    private ?float $pingTimespan;

    /**
     * 使用 SSL 连接.
     */
    private bool $ssl = false;

    /**
     * 证书文件.
     */
    private ?string $sslCertFile;

    /**
     * 证书密钥文件.
     */
    private ?string $sslKeyFile;

    /**
     * 验证服务器端证书。
     */
    private bool $sslVerifyPeer = true;

    /**
     * 允许自签名证书.
     */
    private bool $sslAllowSelfSigned = false;

    /**
     * 服务器主机名称.
     */
    private ?string $sslHostName;

    /**
     * CA 证书.
     */
    private ?string $sslCafile;

    /**
     * 证书目录.
     */
    private ?string $sslCapath;

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
        string $clientId = '',
        int $keepAlive = 60,
        int $protocol = 4,
        bool $clean = true
    ) {
        parent::__construct($username, $password, $will, $clientId, $keepAlive, $protocol, $clean);
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->pingTimespan = $pingTimespan;
    }

    /**
     * Get 主机地址
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * With 主机地址
     *
     * @param string $host 主机地址
     */
    public function withHost(string $host): self
    {
        $result = clone $this;
        $result->host = $host;

        return $result;
    }

    /**
     * Get 端口号.
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * With 端口号.
     *
     * @param int $port 端口号
     */
    public function withPort(int $port): self
    {
        $result = clone $this;
        $result->port = $port;

        return $result;
    }

    /**
     * Get 超时时间，单位：秒.
     */
    public function getTimeout(): ?float
    {
        return $this->timeout;
    }

    /**
     * With 超时时间，单位：秒.
     *
     * @param float $timeout 超时时间，单位：秒
     */
    public function withTimeout(?float $timeout): self
    {
        $result = clone $this;
        $result->timeout = $timeout;

        return $result;
    }

    /**
     * Get ping 时间间隔，为 NULL 则不自动 Ping.
     */
    public function getPingTimespan(): ?float
    {
        return $this->pingTimespan;
    }

    /**
     * With ping 时间间隔，为 NULL 则不自动 Ping.
     *
     * @param float|null $pingTimespan Ping 时间间隔，为 NULL 则不自动 Ping
     */
    public function withPingTimespan(?float $pingTimespan): self
    {
        $result = clone $this;
        $result->pingTimespan = $pingTimespan;

        return $result;
    }

    /**
     * Get 使用 SSL 连接.
     */
    public function getSsl(): bool
    {
        return $this->ssl;
    }

    /**
     * With 使用 SSL 连接.
     *
     * @param bool $ssl 使用 SSL 连接
     */
    public function withSsl(bool $ssl): self
    {
        $result = clone $this;
        $result->ssl = $ssl;

        return $result;
    }

    /**
     * Get 证书文件.
     */
    public function getSslCertFile(): ?string
    {
        return $this->sslCertFile;
    }

    /**
     * With 证书文件.
     *
     * @param string|null $sslCertFile 证书文件
     */
    public function withSslCertFile(?string $sslCertFile): self
    {
        $result = clone $this;
        $result->sslCertFile = $sslCertFile;

        return $result;
    }

    /**
     * Get 证书密钥文件.
     */
    public function getSslKeyFile(): ?string
    {
        return $this->sslKeyFile;
    }

    /**
     * With 证书密钥文件.
     *
     * @param string|null $sslKeyFile 证书密钥文件
     */
    public function withSslKeyFile(?string $sslKeyFile): self
    {
        $result = clone $this;
        $result->sslKeyFile = $sslKeyFile;

        return $result;
    }

    /**
     * Get 验证服务器端证书。
     */
    public function getSslVerifyPeer(): bool
    {
        return $this->sslVerifyPeer;
    }

    /**
     * With 验证服务器端证书。
     *
     * @param bool $sslVerifyPeer 验证服务器端证书
     */
    public function withSslVerifyPeer(bool $sslVerifyPeer): self
    {
        $result = clone $this;
        $result->sslVerifyPeer = $sslVerifyPeer;

        return $result;
    }

    /**
     * Get 允许自签名证书.
     */
    public function getSslAllowSelfSigned(): bool
    {
        return $this->sslAllowSelfSigned;
    }

    /**
     * With 允许自签名证书.
     *
     * @param bool $sslAllowSelfSigned 允许自签名证书
     */
    public function withSslAllowSelfSigned(bool $sslAllowSelfSigned): self
    {
        $result = clone $this;
        $result->sslAllowSelfSigned = $sslAllowSelfSigned;

        return $result;
    }

    /**
     * Get 服务器主机名称.
     */
    public function getSslHostName(): ?string
    {
        return $this->sslHostName;
    }

    /**
     * With 服务器主机名称.
     *
     * @param string|null $sslHostName 服务器主机名称
     */
    public function withSslHostName(?string $sslHostName): self
    {
        $result = clone $this;
        $result->sslHostName = $sslHostName;

        return $result;
    }

    /**
     * Get cA 证书.
     */
    public function getSslCafile(): ?string
    {
        return $this->sslCafile;
    }

    /**
     * With cA 证书.
     *
     * @param string|null $sslCafile CA 证书
     */
    public function withSslCafile(?string $sslCafile): self
    {
        $result = clone $this;
        $result->sslCafile = $sslCafile;

        return $result;
    }

    /**
     * Get 证书目录.
     */
    public function getSslCapath(): ?string
    {
        return $this->sslCapath;
    }

    /**
     * With 证书目录.
     *
     * @param string|null $sslCapath 证书目录
     */
    public function withSslCapath(?string $sslCapath): self
    {
        $result = clone $this;
        $result->sslCapath = $sslCapath;

        return $result;
    }
}
