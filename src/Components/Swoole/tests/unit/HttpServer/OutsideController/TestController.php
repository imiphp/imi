<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\OutsideController;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @Controller
 */
class TestController extends HttpController
{
    /**
     * @Action
     * @Route("/testOutside")
     *
     * @return void
     */
    public function testOutside()
    {
        return [
            'action'    => 'testOutside',
        ];
    }
}
