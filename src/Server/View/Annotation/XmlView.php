<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

/**
 * XML 视图配置注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class XmlView extends BaseViewOption
{
    public function __construct()
    {
    }
}
