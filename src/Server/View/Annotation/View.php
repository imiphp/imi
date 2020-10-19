<?php

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
class View extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'template';

    /**
     * 模版基础路径
     * abc-配置中设定的路径/abc/
     * /abc/-绝对路径.
     *
     * @var string
     */
    public $baseDir;

    /**
     * 模版路径.
     *
     * @var string
     */
    public $template;

    /**
     * 渲染类型.
     *
     * @var string
     */
    public $renderType = 'json';

    /**
     * 附加数据.
     *
     * @var array
     */
    public $data = [];
}
