<?php
namespace Imi\Process;

use Imi\Util\ArrayUtil;
use Imi\Bean\Annotation\Bean;

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
     * @param string $name
     * @param string $process
     * @param array $args
     * @return void
     */
    public function add($name, $process, $args = [])
    {
        $this->processes[$name] = [
            'process'   =>  $process,
            'args'      =>  $args,
        ];
    }

    /**
     * 移除进程
     *
     * @param string $name
     * @return void
     */
    public function remove($name)
    {
        if(isset($this->processes[$name]))
        {
            unset($this->processes[$name]);
        }
        else
        {
            ArrayUtil::remove($this->processes, $name);
        }
    }

    /**
     * 进程是否存在
     *
     * @param string $name
     * @return bool
     */
    public function exists($name): bool
    {
        return isset($this->processes[$name]) || in_array($name, $this->processes);
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
