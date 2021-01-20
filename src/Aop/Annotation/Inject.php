<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\Bean\Annotation\Parser;

/**
 * 对象注入
 * 使用：App::getBean().
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class Inject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * Bean名称或类名.
     *
     * @var string
     */
    public string $name = '';

    /**
     * Bean实例化参数.
     *
     * @var array
     */
    public array $args = [];

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return App::getBean($this->name, ...$this->args);
    }
}
