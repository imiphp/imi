<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 视图注解.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @Parser("Imi\Server\View\Parser\ViewParser")
 *
 * @property string              $renderType 渲染类型
 * @property mixed               $data       附加数据
 * @property BaseViewOption|null $option     视图配置注解
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class View extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'renderType';

    /**
     * @param mixed $data
     */
    public function __construct(?array $__data = null, string $renderType = 'json', $data = [], ?BaseViewOption $option = null)
    {
        parent::__construct(...\func_get_args());
    }
}
