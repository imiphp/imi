<?php
namespace Imi\Test\HttpServer\ApiServer\Controller;

use Imi\RequestContext;
use Imi\Aop\Annotation\Inject;
use Imi\Util\Http\MessageUtil;
use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Middleware;

/**
 * @Controller(prefix="/", singleton=true)
 */
class IndexController extends HttpController
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
     * @return void
     */
    public function index()
    {
        return RequestContext::get('response')->write('imi');
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
            'id'    =>  $id,
        ];
    }

    /**
     * @Action
     * @View(renderType="html", template="html")
     * @return void
     */
    public function html($time)
    {
        return [
            'time'  =>  date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     * @View(renderType="html", baseDir="index/")
     * @return void
     */
    public function html2($time)
    {
        return [
            'time'  =>  date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function json($time)
    {
        return [
            'time'  =>  $time,
            'data'  =>  $this->testService->test($time),
        ];
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
            'get'       =>  $request->get(),
            'post'      =>  $request->post(),
            'cookie'    =>  $request->getCookieParams(),
            'headers'   =>  MessageUtil::headersToStringList($request->getHeaders()),
            'server'    =>  $request->getServerParams(),
            'request'   =>  $request->request(),
            'uri'       =>  (string)$request->getUri(),
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
            'get'       =>  $_GET,
            'post'      =>  $_POST,
            'cookie'    =>  $_COOKIE,
            'server'    =>  $_SERVER,
            'request'   =>  $_REQUEST,
            'session'   =>  $_SESSION,
            'files'     =>  $_FILES,
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
            'get'       =>  $request->get(),
            'post'      =>  $request->post(),
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
            'get'           =>  $request->get(),
            'post'          =>  $request->post(),
            'parsedBody'    =>  $request->getParsedBody(),
            'default'       =>  $default,
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
     * @Middleware(\Imi\Test\HttpServer\ApiServer\Middleware\Middleware1::class)
     * @Middleware({
     *  \Imi\Test\HttpServer\ApiServer\Middleware\Middleware2::class,
     *  \Imi\Test\HttpServer\ApiServer\Middleware\Middleware3::class
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
        foreach($files as $k => $file)
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
     * @return void
     */
    public function executeTimeout()
    {
        sleep(5);
        return [
            'success'    =>  true,
        ];
    }

}
