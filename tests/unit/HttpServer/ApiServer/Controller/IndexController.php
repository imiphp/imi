<?php

namespace Imi\Test\HttpServer\ApiServer\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\Controller\SingletonHttpController;
use Imi\Process\ProcessManager;
use Imi\RequestContext;
use Imi\Server\Http\Message\Response;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Middleware;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\MessageUtil;
use Swoole\Coroutine;

/**
 * @Controller(prefix="/", singleton=true)
 */
class IndexController extends SingletonHttpController
{
    /**
     * @Inject("TestService")
     *
     * @var \Imi\Test\HttpServer\Modules\Test\Service\TestService
     */
    protected $testService;

    /**
     * @Action
     * @Route("/")
     *
     * @return Response
     */
    public function index()
    {
        return RequestContext::get('response')->write('imi');
    }

    /**
     * @Action
     * @Route("/route/{id}")
     *
     * @param string $id
     *
     * @return array
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
     * @param int $time
     *
     * @return array
     */
    public function html($time)
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     * @View(renderType="html", baseDir="index/")
     *
     * @param int $time
     *
     * @return array
     */
    public function html2($time)
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     *
     * @return \Imi\Server\View\Annotation\View
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
     * @return \Imi\Server\View\Annotation\View
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
     * @param int $time
     *
     * @return array
     */
    public function json($time)
    {
        return [
            'time'  => $time,
            'data'  => $this->testService->test($time),
        ];
    }

    /**
     * @Action
     *
     * @return array
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
     * @return array
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
     * @param string $get
     * @param string $post
     * @param int    $default
     *
     * @return array
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
     * @param string $get
     * @param string $post
     * @param string $parsedBody
     * @param int    $default
     *
     * @return array
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
     * @return Response
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
     * @return Response
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
     * @Middleware(\Imi\Test\HttpServer\ApiServer\Middleware\Middleware1::class)
     * @Middleware({
     *  \Imi\Test\HttpServer\ApiServer\Middleware\Middleware2::class,
     *  \Imi\Test\HttpServer\ApiServer\Middleware\Middleware3::class
     * })
     * @Middleware("@test")
     *
     * @return array
     */
    public function middleware()
    {
        return [];
    }

    /**
     * @Action
     *
     * @return Response
     */
    public function redirect()
    {
        return RequestContext::get('response')->redirect('/', StatusCode::MOVED_PERMANENTLY);
    }

    /**
     * @Action
     *
     * @return Response
     */
    public function download()
    {
        return RequestContext::get('response')->sendFile(__FILE__);
    }

    /**
     * @Action
     *
     * @return array
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
                'hash'              => md5($file->getStream()),
            ];
        }

        return $result;
    }

    /**
     * @Action
     *
     * @return array
     */
    public function executeTimeout()
    {
        Coroutine::sleep(5);

        return [
            'success'    => true,
        ];
    }

    /**
     * @Action
     * @Route("/a/{id:[0-9]{1,3}}/{page:\d+}")
     *
     * @param string $id
     * @param string $page
     *
     * @return array
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
     * @param string $name
     * @param string $page
     *
     * @return array
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
     * @return array
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
     * @return mixed
     */
    public function singletonResponse1()
    {
        return $this->response->write('imi niubi-1');
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
        $this->response->setResponseInstance($this->response->write('imi niubi-2'));
    }

    /**
     * @Action
     *
     * @return array
     */
    public function process()
    {
        $process = ProcessManager::getProcessWithManager('CronProcess');

        return [
            'result'    => $process instanceof \Swoole\Process,
        ];
    }

    /**
     * @Action
     * @Route(url="/type/{id}/{name}/{page}")
     *
     * @param string $id
     * @param string $name
     * @param string $page
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
