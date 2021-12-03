<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

/**
 * XML 视图配置注解.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class XmlView extends BaseViewOption
{
    public function __construct(?array $__data = null)
    {
        parent::__construct(...\func_get_args());
    }
}
