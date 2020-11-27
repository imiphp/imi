<?php

namespace Imi\Server\Session;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("SessionCookie")
 */
class SessionCookie
{
    /**
     * Cookie 的 生命周期，以秒为单位。
     *
     * @var int
     */
    public int $lifetime;

    /**
     * 此 cookie 的有效 路径。 on the domain where 设置为“/”表示对于本域上所有的路径此 cookie 都可用。
     *
     * @var string
     */
    public string $path;

    /**
     * Cookie 的作用 域。 例如：“www.php.net”。 如果要让 cookie 在所有的子域中都可用，此参数必须以点（.）开头，例如：“.php.net”。
     *
     * @var string
     */
    public string $domain;

    /**
     * 设置为 TRUE 表示 cookie 仅在使用 安全 链接时可用。
     *
     * @var bool
     */
    public bool $secure;

    /**
     * 设置为 TRUE 表示 PHP 发送 cookie 的时候会使用 httponly 标记。
     *
     * @var bool
     */
    public bool $httponly;

    /**
     * 是否启用 Cookie.
     *
     * @var bool
     */
    public bool $enable = true;

    public function __construct(int $lifetime = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
    {
        $this->lifetime = $lifetime;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httponly = $httponly;
    }
}
