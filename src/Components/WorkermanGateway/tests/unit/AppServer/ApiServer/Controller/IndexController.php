<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\ApiServer\Controller;

use Imi\Controller\SingletonHttpController;
use Imi\RequestContext;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @Controller(prefix="/", singleton=true)
 */
class IndexController extends SingletonHttpController
{
    /**
     * @Action
     * @Route("/")
     *
     * @return mixed
     */
    public function index()
    {
        $response = RequestContext::get('response');
        $response->getBody()->write('imi');

        return $response;
    }
}
