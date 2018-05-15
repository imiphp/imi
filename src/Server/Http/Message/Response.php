<?php
namespace Imi\Server\Http\Message;

use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Stream\MemoryStream;

class Response
{
	/**
	 * swoole响应对象
	 * @var \swoole_http_response
	 */
	protected $swooleResponse;

	/**
	 * 符合psr-7规范的Response对象
	 * @var \Imi\Util\Http\Response
	 */
	protected $psr7Response;

	/**
	 * cookies
	 * @var array
	 */
	protected $cookies = [];

	/**
	 * GZIP启用状态，默认禁用
	 * @var boolean
	 */
	protected $gzipStatus = false;

	/**
	 * gzip压缩等级1-9，默认为5
	 * @var int
	 */
	protected $gzipLevel = 5;
    
    /**
     * 是否已结束请求
     * @var boolean
     */
    protected $isEnded = false;

    public function __construct(\swoole_http_response $response)
    {
		$this->swooleResponse = $response;
		$this->psr7Response = new \Imi\Util\Http\Response();
	}

	/**
	 * 设置cookie
	 * @param string $key
	 * @param string $value
	 * @param integer $expire
	 * @param string $path
	 * @param string $domain
	 * @param boolean $secure
	 * @param boolean $httponly
	 * @return static
	 */
	public function cookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
	{
		$this->cookies[] = [
			'key'		=>	$key,
			'value'		=>	$value,
			'expire'	=>	$expire,
			'path'		=>	$path,
			'domain'	=>	$domain,
			'secure'	=>	$secure,
			'httponly'	=>	$httponly,
		];
		return $this;
	}

	/**
	 * 设置响应头
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function header($name, $value)
	{
		$this->psr7Response = $this->psr7Response->withHeader($name, $value);
		return $this;
	}

	/**
	 * 批量设置响应头
	 * @param array $headers
	 * @return void
	 */
	public function headers($headers)
	{
		foreach($headers as $name => $value)
		{
			$this->psr7Response = $this->psr7Response->withHeader($name, $value);
		}
		return $this;
	}

	/**
	 * 输出内容，但不发送
	 * @param string $body
	 * @return static
	 */
	public function write(string $body)
	{
		$this->psr7Response->getBody()->write($body);
		return $this;
	}

	/**
	 * 清空内容
	 * @return static
	 */
	public function clear()
	{
		$this->psr7Response = $this->psr7Response->withBody(new MemoryStream());
		return $this;
	}
	
	/**
	 * 设置服务器端重定向
	 * 默认状态码为302
	 * @param string $url
	 * @param int $status
	 * @return static
	 */
	public function redirect($url, $status = StatusCode::FOUND)
	{
		$this->psr7Response->withStatus($status)->withHeader('location', $url);
		return $this;
	}

	/**
	 * 发送头部信息，没有特别需求，无需手动调用
	 * @return static
	 */
	public function sendHeaders()
	{
		// cookie
		foreach($this->cookies as $cookie)
		{
			$this->swooleResponse->cookie($cookie['key'], $cookie['value'], $cookie['expire'] ?? 0, $cookie['path'] ?? '/', $cookie['domain'] ?? '', $cookie['secure'] ?? false, $cookie['httponly'] ?? false);
		}
		// header
		foreach($this->psr7Response->getHeaders() as $name => $headers)
		{
			// 截止写这行注释（2018-05-15）时，swoole还不支持多个同名header输出，后面的会覆盖前面的
			foreach($headers as $header)
			{
				$this->swooleResponse->header($name, $header);
			}
		}
		// status
		$this->swooleResponse->status($this->psr7Response->getStatusCode());
		return $this;
	}

	/**
	 * 发送所有响应数据
	 * @return static
	 */
	public function send()
	{
		// gzip支持
		if($this->gzipStatus)
		{
			$this->swooleResponse->gzip($this->gzipLevel);
		}
		$this->sendHeaders();
		$this->swooleResponse->end($this->psr7Response->getBody());
		$this->isEnded = true;
		return $this;
	}

	/**
	 * 发送文件，一般用于文件下载
	 * @param string $filename 要发送的文件名称，文件不存在或没有访问权限sendfile会失败
	 * @param integer $offset 上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
	 * @param integer $length 发送数据的尺寸，默认为整个文件的尺寸
	 * @return static
	 */
	public function sendFile(string $filename, int $offset = 0, int $length = 0)
	{
		$this->sendHeaders();
		$this->swooleResponse->sendfile($filename, $offset, $length);
		$this->isEnded = true;
		return $this;
	}

	/**
	 * 获取swoole响应对象
	 * @return \swoole_http_response
	 */
	public function getSwooleResonse(): \swoole_http_response
	{
		return $this->swooleResponse;
	}

	/**
	 * 获取psr-7响应对象
	 * @return \Imi\Util\Http\Response
	 */
	public function getPsr7Response(): \Imi\Util\Http\Response
	{
		return $this->psr7Response;
	}

	/**
	 * 设置psr-7响应对象
	 * @param \Imi\Util\Http\Response $response
	 * @return void
	 */
	public function setPsr7Response(\Imi\Util\Http\Response $response)
	{
		$this->psr7Response = $response;
	}

	/**
	 * 是否已结束请求
	 * @return boolean
	 */
	public function isEnded()
	{
		return $this->isEnded;
	}
}