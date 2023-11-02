<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 视图注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
#[Parser(className: \Imi\Server\View\Parser\ViewParser::class)]
class View extends Base
{
    public function __construct(
        /**
         * 渲染类型.
         */
        public string $renderType = 'json',
        /**
         * 附加数据.
         *
         * @var mixed
         */
        public $data = [],
        /**
         * 视图配置注解.
         */
        public ?BaseViewOption $option = null
    ) {
    }
}
