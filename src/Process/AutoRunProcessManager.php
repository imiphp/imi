<?php
namespace Imi\Process;

use Imi\Bean\Annotation\Bean;
use Imi\Util\ArrayUtil;

/**
 * 进程管理器，管理跟随服务自动启动的进程
 * 
 * 本类的操作仅在 manager 进程中有效
 * 
 * @Bean("AutoRunProcessManager")
 */
class AutoRunProcessManager
{
    /**
     * 进程列表
     *
     * @var array
     */
    protected $processes = [];

    /**
     * 添加进程
     *
     * @param string $process
     * @return void
     */
    public function add($process)
    {
        $this->processes[] = $process;
    }

    /**
     * 移除进程
     *
     * @param string $process
     * @return void
     */
    public function remove($process)
    {
        ArrayUtil::remove($this->processes, $process);
    }

    /**
     * 进程是否存在
     *
     * @param string $process
     * @return bool
     */
    public function exists($process): bool
    {
        return in_array($process, $this->processes);
    }

    /**
     * Get 进程列表
     *
     * @return array
     */ 
    public function getProcesses(): array
    {
        return $this->processes;
    }

}
