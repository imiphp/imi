<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\Event;
use Imi\Server\Event\PipeMessageEvent;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Server\Event\Listener\IPipeMessageEventListener;
use Imi\Swoole\Server\Event\Param\PipeMessageEventParam;

#[Listener(eventName: SwooleEvents::SERVER_PIPE_MESSAGE)]
class OnPipeMessage implements IPipeMessageEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(PipeMessageEventParam $e): void
    {
        $data = json_decode((string) $e->message, true);
        $action = $data['action'] ?? null;
        if (!$action)
        {
            return;
        }
        Event::dispatch(new PipeMessageEvent('imi.pipe_message.' . $action, [
            'workerId' => $e->workerId,
            'data'     => $data,
        ]));
    }
}
