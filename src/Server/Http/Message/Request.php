<?php

namespace Imi\Server\Http\Message;

use Imi\Config;
use Imi\Util\Http\Contract\IServerRequest;
use Imi\Util\Http\ServerRequest;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;

class Request extends ServerRequest implements IServerRequest
{
    /**
     * swoole的http请求对象
     *
     * @var \Swoole\Http\Request
     */
    protected $swooleRequest;

    /**
     * 对应的服务器.
     *
     * @var \Imi\Server\Http\Server|\Imi\Server\WebSocket\Server
     */
    protected $serverInstance;

    /**
     * 实例映射.
     *
     * @var static[]
     */
    protected static $instanceMap = [];

    public function __construct(\Imi\Server\Base $server, \Swoole\Http\Request $request)
    {
        $this->swooleRequest = $request;
        $this->serverInstance = $server;
        $body = $request->rawContent();
        if (false === $body)
        {
            $body = '';
        }
        parent::__construct($this->getRequestUri(), $request->header, $body, $request->server['request_method'], $this->getRequestProtocol(), $request->server, $request->cookie ?? [], $request->get ?? [], $request->post ?? [], $request->files ?? []);
    }

    /**
     * 获取实例对象
     *
     * @param \Imi\Server\Base     $server
     * @param \Swoole\Http\Request $request
     *
     * @return static
     */
    public static function getInstance(\Imi\Server\Base $server, \Swoole\Http\Request $request)
    {
        $requestHeader = $request->header;
        $requestServer = $request->server;
        $key = $requestHeader['host'] . '#' . $requestServer['path_info'];
        $instanceMap = &static::$instanceMap;
        if (!isset($instanceMap[$key]))
        {
            if (\count($instanceMap) >= Config::get('@app.http.maxRequestCache', 1024))
            {
                array_shift($instanceMap);
            }
            $instanceMap[$key] = new static($server, $request);
        }
        $instance = clone $instanceMap[$key];
        $instance->serverInstance = $server;
        $instance->swooleRequest = $request;
        $instance->get = $request->get ?? [];
        $instance->uri = $instance->uri->withQuery([] === $instance->get ? '' : (http_build_query($instance->get, null, '&')));
        $instance->post = $request->post ?? [];
        $rawContent = $request->rawContent();
        $instance->body = new MemoryStream(false === $rawContent ? '' : $rawContent);
        $instance->parsedBody = null;
        $instance->headerNames = $instance->headers = [];
        $instance->setHeaders($requestHeader);
        $instance->cookies = $request->cookie ?? [];
        $instance->setUploadedFiles($instance, $request->files ?? []);
        $instance->server = $requestServer;
        $instance->protocolVersion = $instance->getRequestProtocol();
        $instance->method = $requestServer['request_method'];

        return $instance;
    }

    /**
     * 获取请求的Uri.
     *
     * @return string
     */
    private function getRequestUri()
    {
        $serverInstance = $this->serverInstance;
        if ($serverInstance instanceof \Imi\Server\Http\Server)
        {
            $scheme = $serverInstance->isSSL() ? 'https' : 'http';
        }
        elseif ($serverInstance instanceof \Imi\Server\WebSocket\Server)
        {
            $scheme = $serverInstance->isSSL() ? 'wss' : 'ws';
        }
        else
        {
            $scheme = 'http';
        }
        $swooleRequest = $this->swooleRequest;
        $get = $swooleRequest->get;

        return Uri::makeUri($swooleRequest->header['host'], $swooleRequest->server['path_info'], null === $get ? '' : (http_build_query($get, null, '&')), null, $scheme);
    }

    /**
     * 获取协议版本号.
     *
     * @return string
     */
    private function getRequestProtocol()
    {
        list(, $protocol) = explode('/', $this->swooleRequest->server['server_protocol'], 2);

        return $protocol;
    }
}
