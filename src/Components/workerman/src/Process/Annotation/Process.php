<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Workerman\Process\Parser\ProcessParser::class)]
class Process extends Base
{
    public function __construct(
        /**
         * 进程名称.
         */
        public string $name = ''
    ) {
    }
}
