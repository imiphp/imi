<?php

namespace Imi\AMQP\Pool;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("AMQP")
 */
class AMQP
{
    /**
     * 默认连接池名.
     *
     * @var string|null
     */
    protected $defaultPoolName;

    /**
     * Get 默认连接池名.
     *
     * @return string|null
     */
    public function getDefaultPoolName(): ?string
    {
        return $this->defaultPoolName;
    }
}
