<?php

namespace Imi\Util\Http\Contract;

use Psr\Http\Message\ServerRequestInterface;

interface IServerRequest extends ServerRequestInterface
{
    /**
     * 获取 GET 参数
     * 当 $name 为 null 时，返回所有.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name = null, $default = null);

    /**
     * 获取 POST 参数
     * 当 $name 为 null 时，返回所有.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function post($name = null, $default = null);

    /**
     * 判断是否存在 GET 参数.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGet($name);

    /**
     * 判断是否存在 POST 参数.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasPost($name);

    /**
     * 获取 REQUEST 参数
     * 当 $name 为 null 时，返回所有
     * REQUEST 中包括：GET/POST/COOKIE.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function request($name = null, $default = null);

    /**
     * 判断是否存在 REQUEST 参数
     * REQUEST 中包括：GET/POST/COOKIE.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasRequest($name);

    /**
     * 获取swoole的请求对象
     *
     * @return \Swoole\Http\Request
     */
    public function getSwooleRequest(): \Swoole\Http\Request;

    /**
     * 获取对应的服务器.
     *
     * @return \Imi\Server\Base
     */
    public function getServerInstance(): \Imi\Server\Base;
}
