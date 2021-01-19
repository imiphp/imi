<?php

declare(strict_types=1);

namespace Imi\Swoole\Http\Message;

use Imi\Server\Http\Message\Request;
use Imi\Swoole\Server\Base;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

class SwooleRequest extends Request
{
    /**
     * swoole的http请求对象
     *
     * @var \Swoole\Http\Request
     */
    protected \Swoole\Http\Request $swooleRequest;

    /**
     * 对应的服务器.
     *
     * @var \Imi\Swoole\Server\Http\Server|\Imi\Swoole\Server\WebSocket\Server
     */
    protected Base $serverInstance;

    public function __construct(\Imi\Swoole\Server\Base $server, \Swoole\Http\Request $request)
    {
        $this->swooleRequest = $request;
        $this->serverInstance = $server;
    }

    /**
     * 初始化协议版本.
     *
     * @return void
     */
    protected function initProtocolVersion()
    {
        [, $this->protocolVersion] = explode('/', $this->swooleRequest->server['server_protocol'], 2);
    }

    /**
     * 初始化 headers.
     *
     * @return void
     */
    protected function initHeaders()
    {
        $this->mergeHeaders($this->swooleRequest->header);
    }

    /**
     * 初始化 body.
     *
     * @return void
     */
    protected function initBody()
    {
        $this->body = new MemoryStream($this->swooleRequest->rawContent() ?: '');
    }

    /**
     * 初始化 uri.
     *
     * @return void
     */
    protected function initUri()
    {
        $serverInstance = $this->serverInstance;
        if ($serverInstance instanceof \Imi\Swoole\Server\Http\Server)
        {
            $scheme = $serverInstance->isSSL() ? 'https' : 'http';
        }
        elseif ($serverInstance instanceof \Imi\Swoole\Server\WebSocket\Server)
        {
            $scheme = $serverInstance->isSSL() ? 'wss' : 'ws';
        }
        else
        {
            $scheme = 'http';
        }
        $swooleRequest = $this->swooleRequest;
        $get = $swooleRequest->get;

        $this->uri = Uri::makeUri($swooleRequest->header['host'], $swooleRequest->server['path_info'], null === $get ? '' : (http_build_query($get, '', '&')), null, $scheme);
    }

    /**
     * 初始化 method.
     *
     * @return void
     */
    protected function initMethod()
    {
        $this->method = $this->swooleRequest->server['request_method'];
    }

    /**
     * 初始化 server.
     *
     * @return void
     */
    protected function initServer()
    {
        $this->server = $this->swooleRequest->server;
    }

    /**
     * 初始化请求参数.
     *
     * @return void
     */
    protected function initRequestParams()
    {
        $request = $this->swooleRequest;
        $this->get = $request->get ?? [];
        $this->post = $request->post ?? [];
        $this->cookies = $request->cookie ?? [];
        $this->request = null;
    }

    /**
     * 初始化上传文件.
     *
     * @return void
     */
    protected function initUploadedFiles()
    {
        $this->setUploadedFiles($this->swooleRequest->files ?? []);
    }

    /**
     * Get swoole的http请求对象
     *
     * @return \Swoole\Http\Request
     */
    public function getSwooleRequest(): \Swoole\Http\Request
    {
        return $this->swooleRequest;
    }

    /**
     * 获取对应的服务器.
     *
     * @return \Imi\Swoole\Server\Base
     */
    public function getServerInstance(): \Imi\Swoole\Server\Base
    {
        return $this->serverInstance;
    }
}
