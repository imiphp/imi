<?php

declare(strict_types=1);

namespace Imi\Hprose\Client;

use Hprose\Socket\Client;
use Imi\Event\Event;
use Imi\Rpc\Client\IRpcClient;
use Imi\Rpc\Client\IService;

/**
 * Hprose Socket 客户端.
 */
class HproseSocketClient implements IRpcClient
{
    /**
     * Client.
     */
    protected ?Client $client;

    /**
     * 配置.
     */
    protected array $options;

    /**
     * 构造方法.
     *
     * @param array $options 配置
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        $this->client = new \Hprose\Socket\Client($this->options['uris'], false);
        Event::trigger('IMI.RPC.HPROSE.CLIENT.OPEN', [
            'client'    => $this->client,
        ], $this);

        return true;
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->client = null;
    }

    /**
     * 是否已连接.
     */
    public function isConnected(): bool
    {
        return true;
    }

    /**
     * 获取实例对象
     */
    public function getInstance(): Client
    {
        return $this->client;
    }

    /**
     * 获取服务对象
     *
     * @param string|null $name 服务名
     */
    public function getService(?string $name = null): IService
    {
        return new HproseService($this, $name);
    }

    /**
     * 获取配置.
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
