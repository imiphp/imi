<?php

declare(strict_types=1);

namespace Imi\Process;

use Imi\Bean\Annotation\Bean;
use Imi\Util\ArrayUtil;

/**
 * 进程管理器，管理跟随服务自动启动的进程.
 *
 * 本类的操作仅在 manager 进程中有效
 *
 * @Bean("AutoRunProcessManager")
 */
class AutoRunProcessManager
{
    /**
     * 进程列表.
     */
    protected array $processes = [];

    /**
     * 添加进程.
     */
    public function add(string $name, string $process, array $args = []): void
    {
        $this->processes[$name] = [
            'process'   => $process,
            'args'      => $args,
        ];
    }

    /**
     * 移除进程.
     */
    public function remove(string $name): void
    {
        $processes = &$this->processes;
        if (isset($processes[$name]))
        {
            unset($processes[$name]);
        }
        else
        {
            ArrayUtil::remove($processes, $name);
        }
    }

    /**
     * 进程是否存在.
     */
    public function exists(string $name): bool
    {
        $processes = $this->processes;

        return isset($processes[$name]) || \in_array($name, $processes);
    }

    /**
     * Get 进程列表.
     */
    public function getProcesses(): array
    {
        return $this->processes;
    }
}
