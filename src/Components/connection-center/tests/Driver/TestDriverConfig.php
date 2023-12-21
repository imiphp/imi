<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Driver;

use Imi\ConnectionCenter\Contract\AbstractConnectionConfig;

class TestDriverConfig extends AbstractConnectionConfig
{
    protected ?bool $test = null;

    protected static function __create(array $config): self
    {
        $object = new self((int) ($config['weight'] ?? 0));
        $object->test = isset($config['test']) ? (bool) $config['test'] : null;

        return $object;
    }

    public function getTest(): ?bool
    {
        return $this->test;
    }
}
