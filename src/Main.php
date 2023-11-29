<?php

declare(strict_types=1);

namespace Imi;

use Imi\Core\Runtime\Event\BuildRuntimeInfoEvent;
use Imi\Core\Runtime\Event\LoadRuntimeInfoEvent;
use Imi\Event\Event;
use Imi\IDEHelper\BuildIDEHelper;
use Imi\Main\BaseMain;
use Imi\Util\ImiPriority;

/**
 * 主类.
 */
class Main extends BaseMain
{
    public function __init(): void
    {
        Event::on('IMI.LOAD_RUNTIME_INFO', static fn (LoadRuntimeInfoEvent $e) => App::newInstance(\Imi\Bean\Listener\LoadRuntimeListener::class)->handle($e), ImiPriority::IMI_MAX);
        Event::on('IMI.BUILD_RUNTIME', static fn (BuildRuntimeInfoEvent $e) => App::newInstance(\Imi\Bean\Listener\BuildRuntimeListener::class)->handle($e), ImiPriority::IMI_MAX);

        Event::on('IMI.LOAD_RUNTIME_INFO', static fn (LoadRuntimeInfoEvent $e) => App::newInstance(\Imi\Aop\Listener\LoadRuntimeListener::class)->handle($e), 19940300);
        Event::on('IMI.BUILD_RUNTIME', static fn (BuildRuntimeInfoEvent $e) => App::newInstance(\Imi\Aop\Listener\BuildRuntimeListener::class)->handle($e), 19940300);

        Event::on('IMI.LOAD_RUNTIME_INFO', static fn (LoadRuntimeInfoEvent $e) => App::newInstance(\Imi\Event\Listener\LoadRuntimeListener::class)->handle($e), 19940100);
        Event::on('IMI.BUILD_RUNTIME', static fn (BuildRuntimeInfoEvent $e) => App::newInstance(\Imi\Event\Listener\BuildRuntimeListener::class)->handle($e), 19940100);

        Event::on('IMI.LOAD_RUNTIME_INFO', static fn (LoadRuntimeInfoEvent $e) => App::newInstance(\Imi\Core\Component\Listener\LoadRuntimeListener::class)->handle($e), ImiPriority::IMI_MAX);
        Event::on('IMI.BUILD_RUNTIME', static fn (BuildRuntimeInfoEvent $e) => App::newInstance(\Imi\Core\Component\Listener\BuildRuntimeListener::class)->handle($e), ImiPriority::IMI_MAX);

        if (Config::get('@app.imi.ideHelper') ?? App::isDebug())
        {
            Event::on('IMI.LOAD_RUNTIME', static fn (LoadRuntimeInfoEvent $e) => App::newInstance(BuildIDEHelper::class)->handle($e), ImiPriority::MIN);
        }
    }

    /**
     * 获取配置.
     */
    public function getConfig(): array
    {
        if (null === $this->config)
        {
            return $this->config = Config::get('@imi');
        }

        return $this->config;
    }
}
