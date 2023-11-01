<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\ApiServer\Controller;

use Imi\RequestContext;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Message\Emitter\SseEmitter;
use Imi\Server\Http\Message\Emitter\SseMessageEvent;
use Imi\Server\Http\Message\Proxy\ResponseProxy;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Middleware;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Server\View\Annotation\View;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\MessageUtil;
use Imi\Util\Stream\MemoryStream;
use Imi\Workerman\Http\Message\WorkermanRequest;

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
        $response = RequestContext::get('response');
        $response->getBody()->write('imi');

        return $response;
    }

    #[Action]
    #[Route(url: '/route/{id}')]
    public function route(string $id): array
    {
        return [
            'id'    => $id,
        ];
    }

    #[Action]
    #[Route(autoEndSlash: true)]
    #[View(renderType: 'html')]
    #[HtmlView(template: 'html')]
    public function html(int $time): array
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    #[Action]
    #[View(renderType: 'html')]
    #[HtmlView(baseDir: 'index/')]
    public function html2(int $time): array
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @return mixed
     */
    #[Action]
    public function renderHtml1()
    {
        return $this->__render('test/a', [
            'name'  => 'yurun',
        ]);
    }

    /**
     * @return mixed
     */
    #[Action]
    public function renderHtml2()
    {
        return $this->__render(\dirname(__DIR__, 2) . '/template/b.html', [
            'name'  => 'imi',
        ]);
    }

    #[Action]
    public function info(): array
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return [
            'get'       => $request->get(),
            'post'      => $request->post(),
            'cookie'    => $request->getCookieParams(),
            'headers'   => MessageUtil::headersToStringList($request->getHeaders()),
            'server'    => $request->getServerParams(),
            'request'   => $request->request(),
            'uri'       => (string) $request->getUri(),
            'appUri'    => (string) $request->getAppUri(),
        ];
    }

    #[Action]
    public function superGlobalsInfo(): array
    {
        return [
            'get'       => $_GET,
            'post'      => $_POST,
            'cookie'    => $_COOKIE,
            'server'    => $_SERVER,
            'request'   => $_REQUEST,
            'session'   => $_SESSION,
            'files'     => $_FILES,
        ];
    }

    #[Action]
    public function info2(string $get, string $post, int $default = 19260817): array
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return [
            'get'       => $request->get(),
            'post'      => $request->post(),
        ];
    }

    /**
     * @param mixed $get
     * @param mixed $post
     * @param mixed $parsedBody
     */
    #[Action]
    public function info3($get, $post, $parsedBody, int $default = 19260817): array
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return [
            'get'           => $request->get(),
            'post'          => $request->post(),
            'parsedBody'    => $request->getParsedBody(),
            'default'       => $default,
        ];
    }

    /**
     * @return mixed
     */
    #[Action]
    public function cookie()
    {
        return RequestContext::get('response')->withCookie('a', '1')
                                            ->withCookie('b', '2', time() + 1)
                                            ->withCookie('c', '3', 0, '/')
                                            ->withCookie('d', '4', 0, '/a')
                                            ->withCookie('e', '5', 0, '/', 'localhost')
                                            ->withCookie('f', '6', 0, '/', '', true)
                                            ->withCookie('g', '7', 0, '/', '', true, true)
        ;
    }

    /**
     * @return mixed
     */
    #[Action]
    public function headers()
    {
        return RequestContext::get('response')->withHeader('a', '1')
                                         ->withAddedHeader('a', '11')
                                         ->withAddedHeader('b', '2')
                                         ->withHeader('c', '3')
                                         ->withoutHeader('c');
    }

    #[Action]
    #[Route(url: '/middleware')]
    #[Middleware(middlewares: 'Imi\\Workerman\\Test\\AppServer\\ApiServer\\Middleware\\Middleware1')]
    #[Middleware(middlewares: ['Imi\\Workerman\\Test\\AppServer\\ApiServer\\Middleware\\Middleware2', 'Imi\\Workerman\\Test\\AppServer\\ApiServer\\Middleware\\Middleware3'])]
    #[Middleware(middlewares: '@test')]
    public function middleware(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    #[Action]
    public function redirect()
    {
        return RequestContext::get('response')->redirect('/', StatusCode::MOVED_PERMANENTLY);
    }

    /**
     * @return mixed
     */
    #[Action]
    public function download(?string $contentType = null, ?string $outputFileName = null)
    {
        return RequestContext::get('response')->sendFile(__FILE__, $contentType, $outputFileName);
    }

    #[Action]
    public function upload(): array
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();
        $result = [];
        foreach ($files as $k => $file)
        {
            $result[$k] = [
                'clientFilename'    => $file->getClientFilename(),
                'clientMediaType'   => $file->getClientMediaType(),
                'error'             => $file->getError(),
                'size'              => $file->getSize(),
                'hash'              => md5($file->getStream()->getContents()),
            ];
        }

        return $result;
    }

    #[Action]
    #[Route(url: '/a/{id:[0-9]{1,3}}/{page:\\d+}')]
    public function regularExpression1(int $id, int $page): array
    {
        return [
            'id'    => $id,
            'page'  => $page,
        ];
    }

    #[Action]
    #[Route(url: '/a/{name:[a-zA-Z]+}/{page}')]
    public function regularExpression2(string $name, int $page): array
    {
        return [
            'name'  => $name,
            'page'  => $page,
        ];
    }

    #[Action]
    public function singletonRequest(): array
    {
        return [
            'get'       => $this->request->get(),
            'post'      => $this->request->post(),
            'cookie'    => $this->request->getCookieParams(),
            'headers'   => MessageUtil::headersToStringList($this->request->getHeaders()),
            'server'    => $this->request->getServerParams(),
            'request'   => $this->request->request(),
            'uri'       => (string) $this->request->getUri(),
        ];
    }

    /**
     * @return mixed
     */
    #[Action]
    public function singletonResponse1()
    {
        $response = ResponseProxy::__getProxyInstance();
        $response->getBody()->write('imi niubi-1');

        return $response;
    }

    #[Action]
    #[View(renderType: 'html')]
    public function singletonResponse2(): void
    {
        ResponseProxy::__setProxyInstance($this->response->withBody(new MemoryStream('imi niubi-2')));
    }

    #[Action]
    #[Route(url: '/type/{id}/{name}/{page}')]
    public function type(int $id, string $name, int $page): array
    {
        return compact('id', 'name', 'page');
    }

    /**
     * 测试重复路由警告.
     */
    #[Action]
    #[Route(url: '/duplicated')]
    public function duplicated1(): void
    {
    }

    /**
     * 测试重复路由警告.
     */
    #[Action]
    #[Route(url: '/duplicated')]
    public function duplicated2(): void
    {
    }

    #[Action]
    public function connectionId(): array
    {
        /** @var WorkermanRequest $request */
        $request = RequestContext::get('request');

        return [
            'id' => $request->getConnection()->id,
        ];
    }

    /**
     * SSE.
     */
    #[Action]
    public function sse(): void
    {
        $this->response->setResponseBodyEmitter(new class() extends SseEmitter {
            protected function task(): void
            {
                $handler = $this->getHandler();
                foreach (range(1, 100) as $i)
                {
                    if (!$handler->send((string) new SseMessageEvent((string) $i)))
                    {
                        throw new \RuntimeException('Send failed');
                    }
                    usleep(10000);
                }
            }
        });
    }
}
