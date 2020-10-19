<?php

namespace Imi\Task\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Task\TaskParam;

/**
 * 任务注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Task\Parser\TaskParser")
 */
class Task extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 任务名称.
     *
     * @var string
     */
    public $name;

    /**
     * 参数类.
     *
     * @var string
     */
    public $paramClass = TaskParam::class;
}
