<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IManagerStartEventListener;
use Imi\Swoole\Server\Event\Param\ManagerStartEventParam;
use Imi\Swoole\Util\Imi as SwooleImi;
use Imi\Util\Imi;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.START", priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class OnManagerStart implements IManagerStartEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(ManagerStartEventParam $e): void
    {
        App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::MANAGER, true);
        SwooleImi::setProcessName('manager');

        // 随机数播种
        mt_srand();
    }
}
