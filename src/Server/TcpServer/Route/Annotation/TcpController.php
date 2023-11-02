<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 控制器注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\TcpServer\Parser\TcpControllerParser::class)]
class TcpController extends Base
{
    public function __construct(
        /**
         * 指定当前控制器允许哪些服务器使用；支持字符串或数组，默认为 null 则不限制.
         *
         * @var string|string[]|null
         */
        public $server = null
    ) {
    }
}
