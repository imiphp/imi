<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Swoole\Process\Parser\ProcessParser::class)]
class Process extends Base
{
    public function __construct(
        /**
         * 进程名称.
         */
        public string $name = '',
        /**
         * 重定向子进程的标准输入和输出；启用此选项后，在子进程内输出内容将不是打印屏幕，而是写入到主进程管道。读取键盘输入将变为从管道中读取数据。默认为阻塞读取。
         */
        public bool $redirectStdinStdout = false,
        /**
         * 管道类型；启用$redirectStdinStdout后，此选项将忽略用户参数，强制为1。如果子进程内没有进程间通信，可以设置为 0.
         */
        public int $pipeType = 2,
        /**
         * 该进程是否只允许存在一个实例.
         */
        public bool $unique = false,
        /**
         * 自动开启协程.
         */
        public bool $co = true
    ) {
    }
}
