<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\HttpServer\ApiServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;

#[Controller(prefix: '/')]
class IndexController extends HttpController
{
    /**
     * @return mixed
     */
    #[Action]
    #[Route(url: '/')]
    public function index()
    {
        $this->response->getBody()->write('imi niubi');

        return $this->response;
    }

    #[Action]
    public function fail(): void
    {
        throw new \RuntimeException('test gg');
    }
}
