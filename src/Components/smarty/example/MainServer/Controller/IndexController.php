<?php

declare(strict_types=1);

namespace Imi\Smarty\Example\MainServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Server\View\Annotation\View;

#[Controller(prefix: '/')]
class IndexController extends HttpController
{
    /**
     * @return mixed
     */
    #[Action]
    #[Route(url: '/')]
    #[View(renderType: 'html')]
    #[HtmlView(template: 'index')]
    public function index()
    {
        $datetime = date('Y-m-d H:i:s');

        return [
            'datetime'  => $datetime,
        ];
    }

    /**
     * @return mixed
     */
    #[Action]
    #[View(renderType: 'html')]
    #[HtmlView(template: 'test')]
    public function test()
    {
        return [
            'content'   => 'imi nb',
        ];
    }

    /**
     * @return mixed
     */
    #[Action]
    public function ping()
    {
        $this->response->getBody()->write('pong');

        return $this->response;
    }
}
