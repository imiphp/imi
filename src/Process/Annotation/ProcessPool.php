<?php

namespace Imi\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程池注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Process\Parser\ProcessPoolParser")
 */
class ProcessPool extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 进程池名称.
     *
     * @var string
     */
    public $name;

    /**
     * 进程数量.
     *
     * @var int
     */
    public $workerNum = 1;

    /**
     * 进程间通信的模式，默认为0表示不使用任何进程间通信特性.
     *
     * @var int
     */
    public $ipcType = 0;

    /**
     * 消息队列key.
     *
     * @var string
     */
    public $msgQueueKey = null;
}
