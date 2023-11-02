<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程池注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Swoole\Process\Parser\ProcessPoolParser::class)]
class ProcessPool extends Base
{
    public function __construct(
        /**
         * 进程池名称.
         */
        public string $name = '',
        /**
         * 进程数量.
         */
        public int $workerNum = 1,
        /**
         * 进程间通信的模式，默认为0表示不使用任何进程间通信特性.
         */
        public int $ipcType = 0,
        /**
         * 消息队列key.
         */
        public ?string $msgQueueKey = null
    ) {
    }
}
