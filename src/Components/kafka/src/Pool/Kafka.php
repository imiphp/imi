<?php

declare(strict_types=1);

namespace Imi\Kafka\Pool;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("Kafka")
 */
class Kafka
{
    /**
     * 默认连接池名.
     */
    protected ?string $defaultPoolName;

    /**
     * Get 默认连接池名.
     */
    public function getDefaultPoolName(): ?string
    {
        return $this->defaultPoolName;
    }
}
