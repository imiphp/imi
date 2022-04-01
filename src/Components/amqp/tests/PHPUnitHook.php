<?php

declare(strict_types=1);

namespace Imi\AMQP\Test;

use Imi\App;
use Imi\Swoole\SwooleApp;
use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use Swoole\Coroutine\Channel;

class PHPUnitHook implements BeforeFirstTestHook, AfterLastTestHook
{
    private Channel $channel;

    public function executeBeforeFirstTest(): void
    {
        $this->channel = $channel = new Channel(1);
        App::run('AMQPApp', SwooleApp::class, static function () use ($channel) {
            $channel->push(1);
            $channel->pop();
        });
        $channel->pop();
    }

    public function executeAfterLastTest(): void
    {
        $this->channel->push(1);
    }
}
