<?php

declare(strict_types=1);

namespace ImiApp\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Util\Stream\MemoryStream;

#[Controller(prefix: '/')]
#[HtmlView(baseDir: 'index/')]
class IndexController extends HttpController
{
    #[Action]
    #[Route(url: '/')]
    public function index(): mixed
    {
        return $this->response
            ->withBody(new MemoryStream('imi'));
    }
}
