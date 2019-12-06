<?php
namespace Imi\Test\RedisSessionServer\ApiServer\Controller;

use Imi\RequestContext;
use Imi\Aop\Annotation\Inject;
use Imi\Util\Http\MessageUtil;
use Imi\Controller\HttpController;
use Imi\Controller\SingletonHttpController;
use Imi\Server\View\Annotation\View;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Middleware;

/**
 * @Controller("/")
 */
class IndexController extends SingletonHttpController
{
    /**
     * @Action
     * @Route("/")
     *
     * @return void
     */
    public function index()
    {
        return RequestContext::get('response')->write('imi');
    }

}
