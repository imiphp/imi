<?php

namespace Imi\Smarty\Example\MainServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     * @Route("/")
     * @View(renderType="html", template="index")
     *
     * @return mixed
     */
    public function index()
    {
        $datetime = date('Y-m-d H:i:s');

        return [
            'datetime'  => $datetime,
        ];
    }

    /**
     * @Action
     * @View(renderType="html", template="test")
     *
     * @return mixed
     */
    public function test()
    {
        return [
            'content'   => 'imi nb',
        ];
    }

    /**
     * @Action
     *
     * @return mixed
     */
    public function ping()
    {
        return $this->response->write('pong');
    }
}
