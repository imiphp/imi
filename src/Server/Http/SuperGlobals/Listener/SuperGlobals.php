<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals\Listener;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Http\SuperGlobals\Cookie;
use Imi\Server\Http\SuperGlobals\Files;
use Imi\Server\Http\SuperGlobals\Get;
use Imi\Server\Http\SuperGlobals\Post;
use Imi\Server\Http\SuperGlobals\Request;
use Imi\Server\Http\SuperGlobals\Server;
use Imi\Server\Http\SuperGlobals\Session;

/**
 * @Bean("SuperGlobals")
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START")
 */
class SuperGlobals implements IWorkerStartEventListener
{
    /**
     * 是否启用 Hook 超全局变量.
     *
     * @var bool
     */
    protected bool $enable = false;

    /**
     * 事件处理方法.
     *
     * @param WorkerStartEventParam $e
     *
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        if ($this->enable)
        {
            $_GET = new Get();
            $_POST = new Post();
            $_REQUEST = new Request();
            $_SESSION = new Session();
            $_COOKIE = new Cookie();
            $_FILES = new Files();
            $_SERVER = new Server($_SERVER);
        }
    }
}
