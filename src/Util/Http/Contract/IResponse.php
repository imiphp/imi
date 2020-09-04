<?php
namespace Imi\Util\Http\Contract;

use Imi\Util\Http\Consts\StatusCode;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

interface IResponse extends ResponseInterface
{
    /**
     * 获取实例对象
     *
     * @param \Imi\Server\Base $server
     * @param \Swoole\Http\Response $response
     * @return static
     */
    public static function getInstance(\Imi\Server\Base $server, \Swoole\Http\Response $response);

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
    public function withCookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false);

    /**
     * 获取 Trailer 列表
     * 
     * @return array
     */
    public function getTrailers();

    /**
     * Trailer 是否存在
     *
     * @param string $name
     * @return bool
     */
    public function hasTrailer($name);

    /**
     * 获取 Trailer 值
     * 
     * @param string $name
     * @return string|null
     */
    public function getTrailer($name);

    /**
     * 获取 Trailer
     * 
     * @param string $name
     * @param string $value
     * @return static
     */
    public function withTrailer($name, $value);

    /**
     * 输出内容，但不发送
     * @param string $content
     * @return static
     */
    public function write(string $content);

    /**
     * 清空内容
     * @return static
     */
    public function clear();
    
    /**
     * 设置服务器端重定向
     * 默认状态码为302
     * @param string $url
     * @param int $status
     * @return static
     */
    public function redirect($url, $status = StatusCode::FOUND);

    /**
     * 发送头部信息，没有特别需求，无需手动调用
     * @return static
     */
    public function sendHeaders();

    /**
     * 发送所有响应数据
     * @return static
     */
    public function send();

    /**
     * 发送文件，一般用于文件下载
     * @param string $filename 要发送的文件名称，文件不存在或没有访问权限sendfile会失败
     * @param integer $offset 上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
     * @param integer $length 发送数据的尺寸，默认为整个文件的尺寸
     * @return static
     */
    public function sendFile(string $filename, int $offset = 0, int $length = 0);

    /**
     * 获取swoole响应对象
     * @return \Swoole\Http\Response
     */
    public function getSwooleResponse(): \Swoole\Http\Response;

    /**
     * 获取对应的服务器
     * @return \Imi\Server\Base
     */
    public function getServerInstance(): \Imi\Server\Base;

    /**
     * 是否已结束请求
     * @return boolean
     */
    public function isEnded();
}