<?php
namespace Imi\Server\Http\Message;

use Imi\Config;
use Imi\Util\Http\Contract\IServerRequest;
use Imi\Util\Uri;
use Imi\Util\Http\ServerRequest;
use Imi\Util\Stream\MemoryStream;

class Request extends ServerRequest implements IServerRequest
{
    /**
     * swoole的http请求对象
     * @var \Swoole\Http\Request
     */
    protected $swooleRequest;

    /**
     * 对应的服务器
     * @var \Imi\Server\Http\Server|\Imi\Server\WebSocket\Server
     */
    protected $serverInstance;

    /**
     * 实例映射
     *
     * @var static[]
     */
    protected static $instanceMap = [];

    public function __construct(\Imi\Server\Base $server, \Swoole\Http\Request $request)
    {
        $this->swooleRequest = $request;
        $this->serverInstance = $server;
        $body = $request->rawContent();
        if(false === $body)
        {
            $body = '';
        }
        parent::__construct($this->getRequestUri(), $request->header, $body, $request->server['request_method'], $this->getRequestProtocol(), $request->server, $request->cookie ?? [], $request->get ?? [], $request->post ?? [], $request->files ?? []);
    }

    /**
     * 获取实例对象
     *
     * @param \Imi\Server\Base $server
     * @param \Swoole\Http\Request $request
     * @return static
     */
    public static function getInstance(\Imi\Server\Base $server, \Swoole\Http\Request $request)
    {
        $key = $request->header['host'] . '#' . $request->server['path_info'];
        if(!isset(static::$instanceMap[$key]))
        {
            if(count(static::$instanceMap) >= Config::get('@app.http.maxRequestCache', 1024))
            {
                array_shift(static::$instanceMap);
            }
            static::$instanceMap[$key] = new static($server, $request);
        }
        $instance = clone static::$instanceMap[$key];
        $instance->serverInstance = $server;
        $instance->swooleRequest = $request;
        $instance->get = $request->get ?? [];
        $instance->uri = $instance->uri->withQuery([] === $instance->get ? '' : (\http_build_query($instance->get, null, '&')));
        $instance->post = $request->post ?? [];
        $instance->body = new MemoryStream($request->rawContent());
        $instance->headerNames = $instance->headers = [];
        $instance->setHeaders($request->header);
        $instance->cookies = $request->cookie ?? [];
        $instance->setUploadedFiles($instance, $request->files ?? []);
        $instance->server = $request->server;
        $instance->protocolVersion = $instance->getRequestProtocol();
        $instance->method = $request->server['request_method'];
        return $instance;
    }

    /**
     * 获取请求的Uri
     * @return string
     */
    private function getRequestUri()
    {
        if($this->serverInstance instanceof \Imi\Server\Http\Server)
        {
            $scheme = $this->serverInstance->isSSL() ? 'https' : 'http';
        }
        else if($this->serverInstance instanceof \Imi\Server\WebSocket\Server)
        {
            $scheme = $this->serverInstance->isSSL() ? 'wss' : 'ws';
        }
        else
        {
            $scheme = 'http';
        }
        return Uri::makeUri($this->swooleRequest->header['host'], $this->swooleRequest->server['path_info'], null === $this->swooleRequest->get ? '' : (\http_build_query($this->swooleRequest->get, null, '&')), null, $scheme);
    }

    /**
     * 获取协议版本号
     * @return string
     */
    private function getRequestProtocol()
    {
        list(, $protocol) = explode('/', $this->swooleRequest->server['server_protocol'], 2);
        return $protocol;
    }

    /**
     * 获取swoole的请求对象
     * @return \Swoole\Http\Request
     */
    public function getSwooleRequest(): \Swoole\Http\Request
    {
        return $this->swooleRequest;
    }

    /**
     * 获取对应的服务器
     * @return \Imi\Server\Base
     */
    public function getServerInstance(): \Imi\Server\Base
    {
        return $this->serverInstance;
    }

}