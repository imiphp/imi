<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Swoole\Task\TaskParam;

/**
 * 任务注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Swoole\Task\Parser\TaskParser::class)]
class Task extends Base
{
    public function __construct(
        /**
         * 任务名称.
         */
        public string $name = '',
        /**
         * 参数类.
         */
        public string $paramClass = TaskParam::class
    ) {
    }
}
