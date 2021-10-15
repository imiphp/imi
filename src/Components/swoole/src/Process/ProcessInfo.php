<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Swoole\Process;

class ProcessInfo
{
    protected string $uniqueId;

    protected string $name;

    protected ?string $alias;

    protected Process $process;

    public function __construct(string $name, ?string $alias, Process $process)
    {
        $this->name = $name;
        $this->alias = $alias;
        $this->process = $process;
        $this->uniqueId = ProcessManager::buildUniqueId($name, $alias);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function getPid(): int
    {
        $info = ProcessManager::readProcessInfo($this->uniqueId);

        return $info['pid'];
    }
}
