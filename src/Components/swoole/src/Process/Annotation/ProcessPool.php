<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程池注解.
 *
 * @Annotation
 *
 * @Target("CLASS")
 *
 * @Parser("Imi\Swoole\Process\Parser\ProcessPoolParser")
 *
 * @property string      $name        进程池名称
 * @property int         $workerNum   进程数量
 * @property int         $ipcType     进程间通信的模式，默认为0表示不使用任何进程间通信特性
 * @property string|null $msgQueueKey 消息队列key
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class ProcessPool extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, string $name = '', int $workerNum = 1, int $ipcType = 0, ?string $msgQueueKey = null)
    {
        parent::__construct(...\func_get_args());
    }
}
