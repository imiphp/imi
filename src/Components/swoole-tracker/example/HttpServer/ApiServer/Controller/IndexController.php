<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\HttpServer\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

/**
 * @Controller("/")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     * @Route("/")
     *
     * @return mixed
     */
    public function index()
    {
        $this->response->getBody()->write('imi niubi');

        return $this->response;
    }

    /**
     * @Action
     *
     * @return void
     */
    public function fail()
    {
        throw new \RuntimeException('test gg');
    }
}
