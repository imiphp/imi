<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals\Listener;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\SuperGlobals\Cookie;
use Imi\Server\Http\SuperGlobals\Files;
use Imi\Server\Http\SuperGlobals\Get;
use Imi\Server\Http\SuperGlobals\Post;
use Imi\Server\Http\SuperGlobals\Request;
use Imi\Server\Http\SuperGlobals\Server;
use Imi\Server\Http\SuperGlobals\Session;

/**
 * @Bean("SuperGlobals")
 */
class SuperGlobals
{
    /**
     * 是否启用 Hook 超全局变量.
     */
    protected bool $enable = false;

    public function bind(): void
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
