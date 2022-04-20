<?php

declare(strict_types=1);

namespace Imi\AMQP\Test;

use Imi\App;
use Imi\Cli\CliApp;
use function Imi\env;
use Imi\Swoole\SwooleApp;
use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class PHPUnitHook implements BeforeFirstTestHook, AfterLastTestHook
{
    private Channel $channel;

    public function executeBeforeFirstTest(): void
    {
        switch (env('AMQP_TEST_MODE'))
        {
            case 'swoole':
                $this->channel = $channel = new Channel(1);
                Coroutine::create(fn () => App::run('AMQPApp', SwooleApp::class, static function () use ($channel) {
                    $channel->push(1);
                    $channel->pop();
                }));
                $channel->pop();
                break;
            case 'workerman':
                App::run('AMQPApp', CliApp::class, static function () {
                });
                break;
        }
    }

    public function executeAfterLastTest(): void
    {
        if (isset($this->channel))
        {
            $this->channel->push(1);
        }
    }
}
