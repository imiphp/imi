<?php

namespace Imi\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Process\Parser\ProcessParser")
 */
class Process extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 进程名称.
     *
     * @var string
     */
    public $name;

    /**
     * 重定向子进程的标准输入和输出。启用此选项后，在子进程内输出内容将不是打印屏幕，而是写入到主进程管道。读取键盘输入将变为从管道中读取数据。默认为阻塞读取。
     *
     * @var bool
     */
    public $redirectStdinStdout = false;

    /**
     * 管道类型，启用$redirectStdinStdout后，此选项将忽略用户参数，强制为1。如果子进程内没有进程间通信，可以设置为 0.
     *
     * @var int
     */
    public $pipeType = 2;

    /**
     * 该进程是否只允许存在一个实例.
     *
     * @var bool
     */
    public $unique = false;

    /**
     * 自动开启协程.
     *
     * @var bool
     */
    public $co = true;
}
