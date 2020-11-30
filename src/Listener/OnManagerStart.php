<?php

declare(strict_types=1);

namespace Imi\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Listener\IManagerStartEventListener;
use Imi\Server\Event\Param\ManagerStartEventParam;
use Imi\Util\Imi;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Imi\Util\Swoole;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.START",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class OnManagerStart implements IManagerStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param ManagerStartEventParam $e
     *
     * @return void
     */
    public function handle(ManagerStartEventParam $e)
    {
        App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::MANAGER, true);
        Imi::setProcessName('manager');

        // 随机数播种
        mt_srand();

        // 进程PID记录
        $fileName = Imi::getRuntimePath(str_replace('\\', '-', App::getNamespace()) . '.pid');
        file_put_contents($fileName, json_encode([
            'masterPID'     => Swoole::getMasterPID(),
            'managerPID'    => Swoole::getManagerPID(),
        ]));
    }
}
