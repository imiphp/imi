<?php

namespace Imi\Swoole\Process;

use Swoole\Process;

class ProcessItem
{
    protected int $id;

    protected string $name;

    protected ?string $alias;

    protected Process $process;

    public function __construct(string $name, ?string $alias, Process $process)
    {
        $this->id = $process->id;
        $this->name = $name;
        $this->alias = $alias;
        $this->process = $process;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }
}
