<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;

use Imi\Aop\Annotation\Inject;
use Imi\HttpValidate\Annotation\HttpValidation;
use Imi\RequestContext;
use Imi\Server\Http\Annotation\ExtractData;
use Imi\Server\Http\Annotation\RequestParam;
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
use Imi\Swoole\Process\ProcessManager;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\MessageUtil;
use Imi\Util\Stream\MemoryStream;
use Imi\Validate\Annotation\Required;
use Swoole\Coroutine;

/**
 * @Controller(prefix="/")
 */
class IndexController extends HttpController
{
    /**
     * @Inject("TestService")
     *
     * @var \Imi\Swoole\Test\HttpServer\Modules\Test\Service\TestService
     */
    protected $testService;

    /**
     * @Action
     *
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

    /**
     * @Action
     *
     * @Route("/route/{id}")
     */
    public function route(int $id): array
    {
        return [
            'id'    => $id,
        ];
    }

    /**
     * @Action
     *
     * @Route(autoEndSlash=true)
     *
     * @View(renderType="html")
     *
     * @HtmlView(template="html")
     */
    public function html(int $time): array
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     *
     * @View(renderType="html")
     *
     * @HtmlView(baseDir="index/")
     */
    public function html2(int $time): array
    {
        return [
            'time'  => date('Y-m-d H:i:s', $time),
        ];
    }

    /**
     * @Action
     *
     * @return mixed
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
     * @return mixed
     */
    public function renderHtml2()
    {
        return $this->__render(\dirname(__DIR__, 2) . '/template/b.html', [
            'name'  => 'imi',
        ]);
    }

    /**
     * @Action
     */
    public function json(int $time): array
    {
        return [
            'time'  => $time,
            'data'  => $this->testService->test($time),
        ];
    }

    /**
     * @Action
     */
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
        ];
    }

    /**
     * @Action
     */
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

    /**
     * @Action
     *
     * @param string $get
     * @param string $post
     * @param mixed  $default
     */
    public function info2($get, $post, $default = 19260817): array
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
     * @param mixed  $default
     */
    public function info3($get, $post, $parsedBody, $default = 19260817): array
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
     * @return mixed
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
     * @return mixed
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
     *
     * @Route("/middleware")
     *
     * @Middleware(\Imi\Swoole\Test\HttpServer\ApiServer\Middleware\Middleware1::class)
     * @Middleware({
     *     \Imi\Swoole\Test\HttpServer\ApiServer\Middleware\Middleware2::class,
     *     \Imi\Swoole\Test\HttpServer\ApiServer\Middleware\Middleware3::class
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
     * @return mixed
     */
    public function redirect()
    {
        return RequestContext::get('response')->redirect('/', StatusCode::MOVED_PERMANENTLY);
    }

    /**
     * @Action
     *
     * @return mixed
     */
    public function download(?string $contentType = null, ?string $outputFileName = null)
    {
        return RequestContext::get('response')->sendFile(__FILE__, $contentType, $outputFileName);
    }

    /**
     * @Action
     */
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

    /**
     * @Action
     */
    public function executeTimeout(): array
    {
        Coroutine::sleep(2);

        return [
            'success'    => true,
        ];
    }

    /**
     * @Action
     *
     * @Route("/a/{id:[0-9]{1,3}}/{page:\d+}")
     */
    public function regularExpression1(int $id, int $page): array
    {
        return [
            'id'    => $id,
            'page'  => $page,
        ];
    }

    /**
     * @Action
     *
     * @Route("/a/{name:[a-zA-Z]+}/{page}")
     */
    public function regularExpression2(string $name, int $page): array
    {
        return [
            'name'  => $name,
            'page'  => $page,
        ];
    }

    /**
     * @Action
     */
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
     * @Action
     *
     * @return mixed
     */
    public function singletonResponse1()
    {
        $response = ResponseProxy::__getProxyInstance();
        $response->getBody()->write('imi niubi-1');

        return $response;
    }

    /**
     * @Action
     *
     * @View(renderType="html")
     */
    public function singletonResponse2(): void
    {
        ResponseProxy::__setProxyInstance($this->response->withBody(new MemoryStream('imi niubi-2')));
    }

    /**
     * @Action
     */
    public function process(): array
    {
        $process = ProcessManager::getProcessWithManager('CronProcess');

        return [
            'result'    => $process instanceof \Swoole\Process,
        ];
    }

    /**
     * @Action
     *
     * @Route(url="/type/{id}/{name}/{page}")
     */
    public function type(int $id, string $name, int $page): array
    {
        return compact('id', 'name', 'page');
    }

    /**
     * @Action
     *
     * @ExtractData(name="$get.id", to="id2")
     * @ExtractData(name="$get.id", to="id3")
     */
    public function extractData(int $id, int $id2, string $id3): array
    {
        return [
            'id'  => $id,
            'id2' => $id2,
            'id3' => $id3,
        ];
    }

    /**
     * @Action
     *
     * @RequestParam(name="$get.id", param="id2")
     * @RequestParam(name="$get.id3", param="id3", required=false, default="imi 666")
     */
    public function requestParam1(int $id, int $id2, string $id3): array
    {
        return [
            'id'  => $id,
            'id2' => $id2,
            'id3' => $id3,
        ];
    }

    /**
     * @Action
     */
    public function requestParam2(
        int $id,
        #[RequestParam(name: '$get.id')]
        int $id2,
        #[RequestParam(name: '$get.id3', required: false, default: 'imi niubi')]
        string $id3
        ): array {
        return [
            'id'  => $id,
            'id2' => $id2,
            'id3' => $id3,
        ];
    }

    /**
     * 测试重复路由警告.
     *
     * @Action
     *
     * @Route("/duplicated")
     */
    public function duplicated1(): void
    {
    }

    /**
     * 测试重复路由警告.
     *
     * @Action
     *
     * @Route("/duplicated")
     */
    public function duplicated2(): void
    {
    }

    /**
     * 忽略大小写.
     *
     * @Action
     *
     * @Route(ignoreCase=true)
     */
    public function ignoreCase(): void
    {
    }

    /**
     * 测试 domain.
     *
     * @Action
     *
     * @Route(domain="localhost")
     */
    public function domain(): void
    {
    }

    /**
     * 测试 domain2.
     *
     * @Action
     *
     * @Route(domain="local{value}")
     */
    public function domain2(string $value): array
    {
        return [
            'value' => $value,
        ];
    }

    /**
     * SSE.
     *
     * @Action
     */
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

    /**
     * @Action
     *
     * @HttpValidation
     */
    public function validateNone(): void
    {
    }

    /**
     * @Action
     *
     * @HttpValidation
     *
     * @Required(name="$get.id")
     */
    public function validate(int $id = 0): array
    {
        return [
            'id' => $id,
        ];
    }
}
