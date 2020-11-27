<?php

namespace Imi\Server\Session;

use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("SessionConfig")
 */
class SessionConfig
{
    /**
     * session名称.
     *
     * @var string
     */
    public string $name;

    /**
     * @RequestInject("SessionCookie")
     *
     * @var \Imi\Server\Session\SessionCookie|null
     */
    public ?SessionCookie $cookie;

    /**
     * 每次请求完成后触发垃圾回收的概率，默认为1%
     * 可取值0~1.0，概率为0%~100%.
     *
     * @var float
     */
    public float $gcProbability;

    /**
     * 最大存活时间，默认30天，单位秒.
     *
     * @var int
     */
    public int $maxLifeTime;

    /**
     * Session 前缀
     *
     * @var string|null
     */
    public ?string $prefix;

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
