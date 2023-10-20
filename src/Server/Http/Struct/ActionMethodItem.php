<?php

declare(strict_types=1);

namespace Imi\Server\Http\Struct;

class ActionMethodItem
{
    /**
     * 类型.
     */
    protected ?string $type = null;

    /**
     * @param mixed $default
     */
    public function __construct(
        /**
         * 参数名.
         */
        protected string $name,
        /**
         * 是否有默认值
         */
        protected bool $hasDefault,
        /**
         * 默认值
         */
        protected $default,
        /**
         * 是否允许为 null.
         */
        protected bool $allowNull, ?\ReflectionType $type)
    {
        if ($type instanceof \ReflectionNamedType)
        {
            $this->type = $type->getName();
        }
    }

    /**
     * Get 参数名.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get 默认值
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get 类型.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Get 是否有默认值.
     */
    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    /**
     * 是否允许为 null.
     */
    public function allowNull(): bool
    {
        return $this->allowNull;
    }
}
