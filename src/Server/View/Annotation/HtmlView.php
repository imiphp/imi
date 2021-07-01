<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

use Imi\Bean\Annotation\Parser;

/**
 * HTML 视图配置注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null $baseDir  模版基础路径；abc-配置中设定的路径/abc/；/abc/-绝对路径
 * @property string|null $template 模版路径
 */
#[\Attribute]
class HtmlView extends BaseViewOption
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'template';

    public function __construct(?array $__data = null, ?string $baseDir = null, ?string $template = null)
    {
        parent::__construct(...\func_get_args());
    }
}
