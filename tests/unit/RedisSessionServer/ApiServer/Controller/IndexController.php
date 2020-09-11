<?php

namespace Imi\Test\RedisSessionServer\ApiServer\Controller;

use Imi\Controller\SingletonHttpController;
use Imi\RequestContext;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Route;

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
