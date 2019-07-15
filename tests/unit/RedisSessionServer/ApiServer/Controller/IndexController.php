<?php
namespace Imi\Test\RedisSessionServer\ApiServer\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\Util\Http\MessageUtil;
use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Middleware;
use Imi\Util\Http\Consts\StatusCode;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     * @Route("/")
     *
     * @return void
     */
    public function index()
    {
        return $this->response->write('imi');
    }

}
