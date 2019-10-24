<?php
namespace Imi\Server\Http\Message;

use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Stream\MemoryStream;

class Response extends \Imi\Util\Http\Response
{
    /**
     * swoole响应对象
     * @var \Swoole\Http\Response
     */
    protected $swooleResponse;

    /**
     * cookies
     * @var array
     */
    protected $cookies = [];

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

    /**
     * 对应的服务器
     * @var \Imi\Server\Base
     */
    protected $serverInstance;

    /**
     * 空对象缓存
     *
     * @var static
     */
    protected static $emptyInstance;

    public function __construct(\Imi\Server\Base $server, \Swoole\Http\Response $response)
    {
        $this->swooleResponse = $response;
        $this->serverInstance = $server;
        parent::__construct();
    }

    /**
     * 获取实例对象
     *
     * @param \Imi\Server\Base $server
     * @param \Swoole\Http\Response $response
     * @return static
     */
    public static function getInstance(\Imi\Server\Base $server, \Swoole\Http\Response $response)
    {
        if(null === static::$emptyInstance)
        {
            static::$emptyInstance = new static($server, $response);
        }
        $instance = clone static::$emptyInstance;
        $instance->serverInstance = $server;
        $instance->swooleResponse = $response;
        return $instance;
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
    public function withCookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
        $self = clone $this;
        $self->cookies[] = [
            'key'       => $key,
            'value'     => $value,
            'expire'    => $expire,
            'path'      => $path,
            'domain'    => $domain,
            'secure'    => $secure,
            'httponly'  => $httponly,
        ];
        return $self;
    }

    /**
     * 输出内容，但不发送
     * @param string $content
     * @return static
     */
    public function write(string $content)
    {
        $body = clone $this->getBody();
        $body->write($content);
        return $this->withBody($body);
    }

    /**
     * 清空内容
     * @return static
     */
    public function clear()
    {
        return $this->withBody(new MemoryStream(''));
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
        return $this->withStatus($status)->withHeader('location', $url);
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
        foreach($this->headers as $name => $headers)
        {
            $this->swooleResponse->header($name, $this->getHeaderLine($name));
        }
        // status
        if(StatusCode::OK !== $this->statusCode)
        {
            $this->swooleResponse->status($this->statusCode);
        }
        return $this;
    }

    /**
     * 发送所有响应数据
     * @return static
     */
    public function send()
    {
        $this->isEnded = true;
        $this->sendHeaders();
        $this->swooleResponse->end($this->getBody());
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
        $this->isEnded = true;
        $this->sendHeaders();
        $this->swooleResponse->sendfile($filename, $offset, $length);
        return $this;
    }

    /**
     * 获取swoole响应对象
     * @return \Swoole\Http\Response
     */
    public function getSwooleResponse(): \Swoole\Http\Response
    {
        return $this->swooleResponse;
    }

    /**
     * 获取对应的服务器
     * @return \Imi\Server\Base
     */
    public function getServerInstance(): \Imi\Server\Base
    {
        return $this->serverInstance;
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