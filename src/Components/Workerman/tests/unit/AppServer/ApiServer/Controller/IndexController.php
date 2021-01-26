<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\ApiServer\Controller;

use Imi\Controller\SingletonHttpController;
use Imi\RequestContext;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Middleware;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\MessageUtil;
use Imi\Util\Stream\MemoryStream;

/**
 * @Controller(prefix="/", singleton=true)
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
        $response = RequestContext::get('response');
        $response->getBody()->write('imi');

        return $response;
    }

    /**
     * @Action
     * @Route("/route/{id}")
     *
     * @return void
     */
    public function route($id)
    {
        return [
            'id'    => $id,
        ];
    }

    /**
     * @Action
     * @Route(autoEndSlash=true)
     * @View(renderType="html", template="html")
     *
     * @return void
     */
    public function html(int $time)
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     * @View(renderType="html", baseDir="index/")
     *
     * @return void
     */
    public function html2(int $time)
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function renderHtml1()
    {
        return $this->__render('test/a', [
            'name'  => 'yurun',
        ]);
    }

    /**
     * @Action
     *
     * @return void
     */
    public function renderHtml2()
    {
        return $this->__render(\dirname(__DIR__, 2) . '/template/b.html', [
            'name'  => 'imi',
        ]);
    }

    /**
     * @Action
     *
     * @return void
     */
    public function info()
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
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function superGlobalsInfo()
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

    /**
     * @Action
     *
     * @return void
     */
    public function info2($get, $post, $default = 19260817)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return [
            'get'       => $request->get(),
            'post'      => $request->post(),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function info3($get, $post, $parsedBody, $default = 19260817)
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
     * @Action
     *
     * @return void
     */
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
     * @Action
     *
     * @return void
     */
    public function headers()
    {
        return RequestContext::get('response')->withHeader('a', '1')
                                         ->withAddedHeader('a', '11')
                                         ->withAddedHeader('b', '2')
                                         ->withHeader('c', '3')
                                         ->withoutHeader('c');
    }

    /**
     * @Action
     * @Route("/middleware")
     * @Middleware(\Imi\Workerman\Test\AppServer\ApiServer\Middleware\Middleware1::class)
     * @Middleware({
     *  \Imi\Workerman\Test\AppServer\ApiServer\Middleware\Middleware2::class,
     *  \Imi\Workerman\Test\AppServer\ApiServer\Middleware\Middleware3::class
     * })
     * @Middleware("@test")
     *
     * @return void
     */
    public function middleware()
    {
        return [];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function redirect()
    {
        return RequestContext::get('response')->redirect('/', StatusCode::MOVED_PERMANENTLY);
    }

    /**
     * @Action
     *
     * @return void
     */
    public function download()
    {
        return RequestContext::get('response')->sendFile(__FILE__);
    }

    /**
     * @Action
     *
     * @return void
     */
    public function upload()
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

    /**
     * @Action
     *
     * @return void
     */
    public function executeTimeout()
    {
        sleep(5);

        return [
            'success'    => true,
        ];
    }

    /**
     * @Action
     * @Route("/a/{id:[0-9]{1,3}}/{page:\d+}")
     *
     * @return void
     */
    public function regularExpression1($id, $page)
    {
        return [
            'id'    => $id,
            'page'  => $page,
        ];
    }

    /**
     * @Action
     * @Route("/a/{name:[a-zA-Z]+}/{page}")
     *
     * @return void
     */
    public function regularExpression2($name, $page)
    {
        return [
            'name'  => $name,
            'page'  => $page,
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function singletonRequest()
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
     * @Action
     *
     * @return void
     */
    public function singletonResponse1()
    {
        $response = $this->response->getResponseInstance();
        $response->getBody()->write('imi niubi-1');

        return $response;
    }

    /**
     * @Action
     *
     * @View(renderType="html")
     *
     * @return void
     */
    public function singletonResponse2()
    {
        $this->response->setResponseInstance($this->response->withBody(new MemoryStream('imi niubi-2')));
    }

    /**
     * @Action
     * @Route(url="/type/{id}/{name}/{page}")
     *
     * @return array
     */
    public function type($id, $name, $page)
    {
        return compact('id', 'name', 'page');
    }

    /**
     * 测试重复路由警告.
     *
     * @Action
     * @Route("/duplicated")
     *
     * @return void
     */
    public function duplicated1()
    {
    }

    /**
     * 测试重复路由警告.
     *
     * @Action
     * @Route("/duplicated")
     *
     * @return void
     */
    public function duplicated2()
    {
    }
}
