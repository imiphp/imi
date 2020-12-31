<?php

declare(strict_types=1);

namespace Imi\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\Event;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Server\Event\Param\AppInitEventParam;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Util\Imi;
use Imi\Util\Swoole;
use Imi\Worker;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class AfterWorkerStart implements IWorkerStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        // 项目初始化事件
        if (!$e->server->getSwooleServer()->taskworker)
        {
            $initFlagFile = Imi::getRuntimePath(str_replace('\\', '-', App::getNamespace()) . '.app.init');
            if (0 === Worker::getWorkerId() && !$this->checkInitFlagFile($initFlagFile))
            {
                Event::trigger('IMI.APP.INIT', [
                ], $e->getTarget(), AppInitEventParam::class);

                file_put_contents($initFlagFile, Swoole::getMasterPID());

                (new ConsoleOutput())->writeln('<info>App Inited</info>');
            }
        }
        // worker 初始化
        Worker::inited();
    }

    /**
     * 检测是否当前服务已初始化.
     *
     * @param string $initFlagFile
     *
     * @return bool
     */
    private function checkInitFlagFile(string $initFlagFile): bool
    {
        return is_file($initFlagFile) && file_get_contents($initFlagFile) == Swoole::getMasterPID();
    }
}
