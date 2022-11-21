<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\OutsideController;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @Controller(server="main")
 */
class TestController extends HttpController
{
    /**
     * @Action
     *
     * @Route("/testOutside")
     */
    public function testOutside(): array
    {
        return [
            'action'    => 'testOutside',
        ];
    }
}
