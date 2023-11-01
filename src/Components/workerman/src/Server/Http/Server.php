<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Util\ImiPriority;
use Imi\Workerman\Http\Message\WorkermanRequest;
use Imi\Workerman\Http\Message\WorkermanResponse;
use Imi\Workerman\Server\Base;
use Imi\Workerman\Server\Http\Listener\BeforeRequest;
use Imi\Workerman\Server\Protocol\WorkermanHttp;
use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Http\Response;

#[Bean(name: 'WorkermanHttpServer')]
class Server extends Base
{
    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker->protocol = WorkermanHttp::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * {@inheritDoc}
     */
    public function isLongConnection(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function bindEvents(): void
    {
        parent::bindEvents();
        if (!App::get('has_imi_workerman_http_request_event', false))
        {
            Event::on('IMI.WORKERMAN.SERVER.HTTP.REQUEST', [new BeforeRequest(), 'handle'], ImiPriority::IMI_MAX);
            App::set('has_imi_workerman_http_request_event', true);
        }
        $this->worker->onMessage = function (ConnectionInterface $connection, $data): void {
            try
            {
                $worker = $this->worker;
                // @phpstan-ignore-next-line
                $request = new WorkermanRequest($worker, $connection, $data);
                // @phpstan-ignore-next-line
                $response = new WorkermanResponse($worker, $connection, new Response(), $request);
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'request'  => $request,
                    'response' => $response,
                ]);
                Event::trigger('IMI.WORKERMAN.SERVER.HTTP.REQUEST', [
                    'server'   => $this,
                    'request'  => $request,
                    'response' => $response,
                ], $this);
            }
            catch (\Throwable $th)
            {
                // @phpstan-ignore-next-line
                if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                {
                    Log::error($th);
                }
            }
            finally
            {
                RequestContext::destroy();
            }
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function getWorkerScheme(): string
    {
        return 'http';
    }
}
