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
    protected ?Client $client = null;

    /**
     * 配置.
     */
    protected array $options;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->client = null;
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected(): bool
    {
        return true;
    }

    /**
     * 实时检查是否已连接.
     */
    public function checkConnected(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): Client
    {
        return $this->client;
    }

    /**
     * {@inheritDoc}
     */
    public function getService(?string $name = null): IService
    {
        return new HproseService($this, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
