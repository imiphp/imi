<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

use Imi\Util\ClassObject;

/**
 * 注解基类.
 */
abstract class Base implements \JsonSerializable
{
    /**
     * 注解别名.
     *
     * @var string|string[]|null
     */
    protected string|array|null $__alias = null;

    /**
     * @return string|string[]|null
     */
    public function getAlias(): string|array|null
    {
        return $this->__alias;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return $this;
    }

    /**
     * 将当前对象作为数组返回.
     */
    public function toArray(): array
    {
        return ClassObject::getPublicProperties($this);
    }
}
