<?php
namespace Imi\Cron;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Args;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;

/**
 * 定时任务管理器
 * 
 * @Bean("CronManager")
 */
class CronManager
{
    /**
     * 注入的任务列表
     *
     * @var array
     */
    protected $tasks = [];

    /**
     * socket 文件路径
     * 
     * 不支持 samba 文件共享
     *
     * @var string
     */
    protected $socketFile;

    /**
     * 真实的任务对象列表
     *
     * @var \Imi\Cron\CronTask[]
     */
    private $realTasks;

    public function __init()
    {
        if(null === $this->socketFile)
        {
            if(ProcessType::PROCESS === App::get(ProcessAppContexts::PROCESS_TYPE))
            {
                $this->socketFile = Args::get('cronSock');
                if(!$this->socketFile)
                {
                    throw new \InvalidArgumentException('In process to run cron, you must have arg cronSock');
                }
            }
            else
            {
                $this->socketFile = '/tmp/imi.' . App::get(ProcessAppContexts::MASTER_PID) . '.cron.sock';
            }
        }
        $this->realTasks = [];
        foreach($this->tasks as $id => $task)
        {
            $this->realTasks[$id] = new CronTask($id, $task['type'], $task['task'], $task['cron'], $task['data'] ?? null, $task['lockExpire'] ?? 120, $task['unique'] ?? false, $task['redisPool'] ?? null, $task['lockWaitTimeout'] ?? 10, $task['force'] ?? false);
        }
    }

    /**
     * 增加 Cron 任务
     *
     * @param string $id
     * @param string $type
     * @param callable|string $task
     * @param array $cronRules
     * @param mixed $data
     * @param float $lockExpire
     * @param string|null $unique
     * @param string|null $redisPool
     * @param float $lockWaitTimeout
     * @param bool $force
     * @return void
     */
    public function addCron(string $id, string $type, $task, array $cronRules, $data, float $lockExpire = 3, $unique = null, $redisPool = null, float $lockWaitTimeout = 3, bool $force = false)
    {
        if(isset($this->tasks[$id]))
        {
            throw new \RuntimeException(sprintf('Cron id %s already exists', $id));
        }
        $this->realTasks[$id] = new CronTask($id, $type, $task, $cronRules, $data, $lockExpire, $unique, $redisPool, $lockWaitTimeout, $force);
    }

    /**
     * Get 真实的任务对象列表
     *
     * @return \Imi\Cron\CronTask[]
     */ 
    public function getRealTasks()
    {
        return $this->realTasks;
    }

    /**
     * 获取任务对象
     *
     * @param string $id
     * @return \Imi\Cron\CronTask|null
     */
    public function getTask($id)
    {
        return $this->realTasks[$id] ?? null;
    }

    /**
     * socket 文件路径
     *
     * @return string
     */ 
    public function getSocketFile()
    {
        return $this->socketFile;
    }

}
