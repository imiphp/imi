<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component;

use Imi\App;
use Imi\Swoole\SwooleApp;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class PHPUnitHook implements Extension
{
    private Channel $channel;

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new class($this->executeBeforeFirstTest(...)) implements ExecutionStartedSubscriber {
            public function __construct(private \Closure $callback)
            {
            }

            public function notify(ExecutionStarted $event): void
            {
                ($this->callback)($event);
            }
        });
        $facade->registerSubscriber(new class($this->executeAfterLastTest(...)) implements ExecutionFinishedSubscriber {
            public function __construct(private \Closure $callback)
            {
            }

            public function notify(ExecutionFinished $event): void
            {
                ($this->callback)($event);
            }
        });
    }

    public function executeBeforeFirstTest(): void
    {
        $this->channel = $channel = new Channel(1);
        Coroutine::create(static fn () => App::run('Imi\Swoole\Test\Component', SwooleApp::class, static function () use ($channel): void {
            $channel->push(1);
            $channel->pop();
        }));
        $channel->pop();
    }

    public function executeAfterLastTest(): void
    {
        $this->channel->push(1);
    }
}
