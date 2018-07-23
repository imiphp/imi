<?php
namespace Imi\Server\Http\Message;

use Imi\Util\Uri;
use Imi\Util\Http\ServerRequest;

class Request extends ServerRequest
{
    /**
     * swoole的http请求对象
     * @var \swoole_http_request
     */
    protected $swooleRequest;

    /**
     * 对应的服务器
     * @var \Imi\Server\Base
     */
    protected $serverInstance;

    public function __construct(\Imi\Server\Base $server, \swoole_http_request $request)
    {
        $this->swooleRequest = $request;
        $this->serverInstance = $server;
        $body = $request->rawContent();
        if(false === $body)
        {
            $body = '';
        }
        parent::__construct($this->getRequestUri(), $request->header, $body, strtoupper($request->server['request_method']), $this->getRequestProtocol(), $request->server, $request->cookie ?? [], $request->get ?? [], $request->post ?? [], $request->files ?? []);
    }

    /**
     * 获取请求的Uri
     * @return string
     */
    private function getRequestUri()
    {
        $isHttps = isset($this->swooleRequest->server['https']) && 'on' === $this->swooleRequest->server['https'];
        return Uri::makeUri($this->swooleRequest->header['host'], $this->swooleRequest->server['path_info'], \http_build_query($this->swooleRequest->get ?? [], null, '&'), null, $isHttps ? 'https' : 'http');
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
     * @return \swoole_http_request
     */
    public function getSwooleRequest(): \swoole_http_request
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