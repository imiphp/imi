<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Config;

/**
 * 通用验证条件
 * 传入回调进行验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 *
 * @property string|null $name          参数名称；属性注解可省略
 * @property bool        $optional      非必验证，只有当值存在才验证
 * @property mixed       $default       当值不符合条件时的默认值
 * @property bool        $inverseResult 对结果取反
 * @property string      $message       当验证条件不符合时的信息；支持代入{:value}原始值；支持代入{:data.xxx}所有数据中的某项；支持以{name}这样的形式，代入注解参数值
 * @property callable    $callable      验证回调
 * @property array       $args          参数名数组；支持代入{:value}原始值；支持代入{:data}所有数据；支持代入{:data.xxx}所有数据中的某项；支持以{name}这样的形式，代入注解参数值；如果没有{}，则原样传值
 * @property string|null $exception     异常类
 * @property int|null    $exCode        异常编码
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Condition extends Base
{
    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, ?string $name = null, bool $optional = false, $default = null, bool $inverseResult = false, string $message = '{name} validate failed', ?callable $callable = null, array $args = ['{:value}'], ?string $exception = null, ?int $exCode = null)
    {
        parent::__construct(...\func_get_args());
        if (null === $this->exception)
        {
            $this->exception = Config::get('@app.validation.exception', \InvalidArgumentException::class);
        }
        if (null === $this->exCode)
        {
            $this->exCode = Config::get('@app.validation.exCode', 0);
        }
    }
}
