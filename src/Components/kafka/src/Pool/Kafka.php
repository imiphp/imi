<?php

declare(strict_types=1);

namespace Imi\Kafka\Pool;

use Imi\Bean\Annotation\Bean;

#[Bean(name: 'Kafka')]
class Kafka
{
    /**
     * 默认连接池名.
     */
    protected ?string $defaultPoolName = null;

    /**
     * Get 默认连接池名.
     */
    public function getDefaultPoolName(): ?string
    {
        return $this->defaultPoolName;
    }
}
