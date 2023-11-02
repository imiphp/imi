<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

/**
 * HTML 视图配置注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class HtmlView extends BaseViewOption
{
    public function __construct(
        /**
         * 模版基础路径；abc-配置中设定的路径/abc/；/abc/-绝对路径.
         */
        public ?string $baseDir = null,
        /**
         * 模版路径.
         */
        public ?string $template = null
    ) {
    }
}
