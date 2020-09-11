<?php

namespace Imi\Test\HttpServer\OutsideController;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Route;

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
