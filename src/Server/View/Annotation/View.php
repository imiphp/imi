<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 视图注解.
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @Parser("Imi\Server\View\Parser\ViewParser")
 */
#[\Attribute]
class View extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'renderType';

    /**
     * 渲染类型.
     */
    public string $renderType = 'json';

    /**
     * 附加数据.
     *
     * @var mixed
     */
    public $data = [];

    /**
     * 视图配置注解.
     */
    public ?BaseViewOption $option = null;

    /**
     * @param mixed $data
     */
    public function __construct(?array $__data = null, string $renderType = 'json', $data = [], ?BaseViewOption $option = null)
    {
        parent::__construct(...\func_get_args());
    }
}
