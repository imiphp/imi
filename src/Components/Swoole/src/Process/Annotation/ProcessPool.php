<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程池注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Swoole\Process\Parser\ProcessPoolParser")
 */
#[\Attribute]
class ProcessPool extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 进程池名称.
     *
     * @var string
     */
    public string $name = '';

    /**
     * 进程数量.
     *
     * @var int
     */
    public int $workerNum = 1;

    /**
     * 进程间通信的模式，默认为0表示不使用任何进程间通信特性.
     *
     * @var int
     */
    public int $ipcType = 0;

    /**
     * 消息队列key.
     *
     * @var string|null
     */
    public ?string $msgQueueKey = null;

    public function __construct(?array $__data = null, string $name = '', int $workerNum = 1, int $ipcType = 0, ?string $msgQueueKey = null)
    {
        parent::__construct(...\func_get_args());
    }
}
