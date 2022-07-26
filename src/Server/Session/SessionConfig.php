<?php

declare(strict_types=1);

namespace Imi\Server\Session;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Annotation\ServerInject;

/**
 * @Bean(name="SessionConfig", recursion=false)
 */
class SessionConfig
{
    /**
     * session名称.
     */
    public string $name = '';

    /**
     * @ServerInject("SessionCookie")
     */
    public SessionCookie $cookie;

    /**
     * 每次请求完成后触发垃圾回收的概率，默认为1%
     * 可取值0~1.0，概率为0%~100%.
     */
    public float $gcProbability = 0;

    /**
     * 最大存活时间，默认30天，单位秒.
     */
    public int $maxLifeTime = 0;

    /**
     * Session 前缀
     */
    public ?string $prefix = null;

    public function __construct(string $name = 'imisid', ?SessionCookie $cookie = null, float $gcProbability = 0.01, int $maxLifeTime = 86400 * 30, ?string $prefix = null)
    {
        $this->name = $name;
        if (null !== $cookie)
        {
            $this->cookie = $cookie;
        }
        $this->gcProbability = $gcProbability;
        $this->maxLifeTime = $maxLifeTime;
        $this->prefix = $prefix;
    }
}
