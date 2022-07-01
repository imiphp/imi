<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\App;
use Imi\Event\Event;
use Imi\Swoole\Util\Imi;
use RuntimeException;

/**
 * 进程池管理类.
 */
class ProcessPoolManager
{
    private static array $map = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map): void
    {
        self::$map = $map;
    }

    /**
     * 增加映射关系.
     */
    public static function add(string $name, string $className, array $options): void
    {
        self::$map[$name] = [
            'className' => $className,
            'options'   => $options,
        ];
    }

    /**
     * 获取配置.
     */
    public static function get(string $name): ?array
    {
        return self::$map[$name] ?? null;
    }

    /**
     * 创建进程池
     * 本方法无法在控制器中使用
     * 返回\Swoole\Process\Pool对象实例.
     *
     * @param int|null $workerNum 指定工作进程的数量
     * @param int|null $ipcType   进程间通信的模式，默认为0表示不使用任何进程间通信特性
     */
    public static function create(string $name, ?int $workerNum = null, array $args = [], ?int $ipcType = 0, ?string $msgQueueKey = null): \Swoole\Process\Pool
    {
        $processPoolOption = self::get($name);
        if (null === $processPoolOption)
        {
            throw new RuntimeException(sprintf('Not found process pool %s', $name));
        }
        if (null === $workerNum)
        {
            $workerNum = $processPoolOption['options']['workerNum'];
        }
        if (null === $ipcType)
        {
            $ipcType = $processPoolOption['options']['ipcType'];
        }
        if (null === $msgQueueKey)
        {
            $msgQueueKey = $processPoolOption['options']['msgQueueKey'];
        }

        $pool = new \Swoole\Process\Pool($workerNum, $ipcType, $msgQueueKey);

        $pool->on('WorkerStart', static function (\Swoole\Process\Pool $pool, int $workerId) use ($name, $workerNum, $args, $ipcType, $msgQueueKey, $processPoolOption) {
            Imi::setProcessName('processPool', [
                'processPoolName'   => $name,
                'workerId'          => $workerId,
            ]);
            // 随机数播种
            mt_srand();
            \Swoole\Coroutine\run(static function () use ($pool, $workerId, $name, $workerNum, $args, $ipcType, $msgQueueKey, $processPoolOption) {
                $processInstance = App::newInstance($processPoolOption['className'], $args);
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
        });

        $pool->on('WorkerStop', imiCallable(static function (\Swoole\Process\Pool $pool, int $workerId) use ($name, $workerNum, $args, $ipcType, $msgQueueKey) {
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
