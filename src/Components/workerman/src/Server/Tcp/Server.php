<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Tcp;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Server\TcpServer\Contract\ITcpServer;
use Imi\Workerman\Server\Base;
use Imi\Workerman\Server\Tcp\Event\WorkermanTcpMessageEvent;
use Workerman\Connection\TcpConnection;

#[Bean(name: 'WorkermanTcpServer')]
class Server extends Base implements ITcpServer
{
    /**
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::TCP;
    }

    /**
     * {@inheritDoc}
     */
    public function isLongConnection(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function bindEvents(): void
    {
        parent::bindEvents();

        $this->worker->onMessage = function (TcpConnection $connection, string $data): void {
            try
            {
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'       => $this,
                    'clientId'     => $clientId,
                ]);
                Event::dispatch(new WorkermanTcpMessageEvent($this, $clientId, $data, $connection));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                // @phpstan-ignore-next-line
                if (true !== $this->getBean('TcpErrorHandler')->handle($th))
                {
                    Log::error($th);
                }
            }
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function getWorkerScheme(): string
    {
        return 'tcp';
    }

    /**
     * {@inheritDoc}
     */
    public function send(int|string $clientId, string $data): bool
    {
        /** @var TcpConnection|null $connection */
        $connection = $this->worker->connections[$clientId] ?? null;
        if (!$connection)
        {
            throw new \RuntimeException(sprintf('Connection %s does not exists', $clientId));
        }

        return false !== $connection->send($data, true);
    }
}
