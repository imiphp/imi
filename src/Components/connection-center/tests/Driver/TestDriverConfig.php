<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Driver;

use Imi\ConnectionCenter\Contract\IConnectionConfig;

class TestDriverConfig implements IConnectionConfig
{
    protected ?bool $test = null;

    public static function createFromArray(array $config): self
    {
        $object = new self();
        $object->test = $config['test'] ?? null;

        return $object;
    }

    public function getTest(): ?bool
    {
        return $this->test;
    }
}
