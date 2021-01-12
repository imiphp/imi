<?php

declare(strict_types=1);

namespace Imi\Process;

use Imi\Bean\BeanFactory;
use Imi\Bean\Scanner;
use Imi\Event\Event;
use Imi\Process\Parser\ProcessPoolParser;
use Imi\Util\Imi;

/**
 * 进程池管理类.
 */
class ProcessPoolManager
{
    private function __construct()
    {
    }

    /**
     * 创建进程池
     * 本方法无法在控制器中使用
     * 返回\Swoole\Process\Pool对象实例.
     *
     * @param string      $name
     * @param int|null    $workerNum   指定工作进程的数量
     * @param array       $args
     * @param int         $ipcType     进程间通信的模式，默认为0表示不使用任何进程间通信特性
     * @param string|null $msgQueueKey
     *
     * @return \Swoole\Process\Pool
     */
    public static function create(string $name, ?int $workerNum = null, array $args = [], int $ipcType = 0, ?string $msgQueueKey = null): \Swoole\Process\Pool
    {
        $processPoolOption = ProcessPoolParser::getInstance()->getProcessPool($name);
        if (null === $processPoolOption)
        {
            return null;
        }
        if (null === $workerNum)
        {
            $workerNum = $processPoolOption['ProcessPool']->workerNum;
        }
        if (null === $ipcType)
        {
            $ipcType = $processPoolOption['ProcessPool']->ipcType;
        }
        if (null === $msgQueueKey)
        {
            $msgQueueKey = $processPoolOption['ProcessPool']->msgQueueKey;
        }

        $pool = new \Swoole\Process\Pool($workerNum, $ipcType, $msgQueueKey);

        $pool->on('WorkerStart', function (\Swoole\Process\Pool $pool, int $workerId) use ($name, $workerNum, $args, $ipcType, $msgQueueKey, $processPoolOption) {
            Imi::setProcessName('processPool', [
                'processPoolName'   => $name,
                'workerId'          => $workerId,
            ]);
            // 强制开启进程协程化
            \Swoole\Runtime::enableCoroutine(true);
            // 随机数播种
            mt_srand();
            imigo(function () use ($pool, $workerId, $name, $workerNum, $args, $ipcType, $msgQueueKey, $processPoolOption) {
                $processInstance = BeanFactory::newInstance($processPoolOption['className'], $args);
                // 加载服务器注解
                Scanner::scanVendor();
                Scanner::scanApp();
                // 进程开始事件
                Event::trigger('IMI.PROCESS_POOL.PROCESS.BEGIN', [
                    'name'          => $name,
                    'pool'          => $pool,
                    'workerId'      => $workerId,
                    'workerNum'     => $workerNum,
                    'args'          => $args,
                    'ipcType'       => $ipcType,
                    'msgQueueKey'   => $msgQueueKey,
                ]);
                // 执行任务
                $processInstance->run($pool, $workerId, $name, $workerNum, $args, $ipcType, $msgQueueKey);
            });
            \Swoole\Event::wait();
        });

        $pool->on('WorkerStop', imiCallable(function (\Swoole\Process\Pool $pool, int $workerId) use ($name, $workerNum, $args, $ipcType, $msgQueueKey) {
            // 进程结束事件
            Event::trigger('IMI.PROCESS_POOL.PROCESS.END', [
                'name'          => $name,
                'pool'          => $pool,
                'workerId'      => $workerId,
                'workerNum'     => $workerNum,
                'args'          => $args,
                'ipcType'       => $ipcType,
                'msgQueueKey'   => $msgQueueKey,
            ]);
        }, true));

        return $pool;
    }
}
