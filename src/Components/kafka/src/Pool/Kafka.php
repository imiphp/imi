<?php

namespace Imi\Kafka\Pool;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("Kafka")
 */
class Kafka
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
