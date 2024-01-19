<?php

declare(strict_types=1);

namespace Imi\Db\ConnectionCenter;

use Imi\ConnectionCenter\Contract\AbstractConnectionConfig;

class DatabaseDriverConfig extends AbstractConnectionConfig
{
    public function __construct(
        public readonly ?string $dsn,
        public readonly string $host,
        public readonly ?int $port,
        public readonly ?string $username,
        public readonly ?string $password,
        public readonly ?string $database,
        public readonly ?string $charset,
        public readonly array $initSqls,
        public readonly string $prefix,
        public readonly array $option,
        float $weight = 0
    ) {
        parent::__construct($weight);
    }

    protected static function __create(array $config): self
    {
        return new self(
            $config['dsn'] ?? null,
            $config['host'] ?? '127.0.0.1',
            isset($config['port']) ? (int) $config['port'] : null,
            $config['username'] ?? null,
            $config['password'] ?? null,
            $config['database'] ?? null,
            $config['charset'] ?? null,
            $config['initSqls'] ?? [],
            $config['prefix'] ?? '',
            $config,
            (int) ($config['weight'] ?? 0),
        );
    }
}
