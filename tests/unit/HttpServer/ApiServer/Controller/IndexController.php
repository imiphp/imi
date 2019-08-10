<?php
namespace Imi\Test\HttpServer\ApiServer\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\Util\Http\MessageUtil;
use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Middleware;
use Imi\Util\Http\Consts\StatusCode;

/**
 * @Controller("/")
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
        return $this->response->write('imi');
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
        return [
            'get'       =>  $this->request->get(),
            'post'      =>  $this->request->post(),
            'cookie'    =>  $this->request->getCookieParams(),
            'headers'   =>  MessageUtil::headersToStringList($this->request->getHeaders()),
            'server'    =>  $this->request->getServerParams(),
            'request'   =>  $this->request->request(),
        ];
    }

    /**
     * @Action
     *
     * @return void
     */
    public function cookie()
    {
        $this->response = $this->response->withCookie('a', '1')
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
        $this->response = $this->response->withHeader('a', '1')
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
        $this->response = $this->response->redirect('/', StatusCode::MOVED_PERMANENTLY);
    }

    /**
     * @Action
     *
     * @return void
     */
    public function download()
    {
        $this->response = $this->response->sendFile(__FILE__);
    }

    /**
     * @Action
     *
     * @return void
     */
    public function upload()
    {
        $files = $this->request->getUploadedFiles();
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

}
