<?php

declare(strict_types=1);

namespace Imi\Server\Http\Struct;

use ReflectionNamedType;

class ActionMethodItem
{
    /**
     * 参数名.
     */
    protected string $name = '';

    /**
     * 是否有默认值
     */
    protected bool $hasDefault = false;

    /**
     * 默认值
     *
     * @var mixed
     */
    protected $default;

    /**
     * 类型.
     */
    protected ?string $type = null;

    /**
     * @param mixed $default
     */
    public function __construct(string $name, bool $hasDefault, $default, ?\ReflectionType $type)
    {
        $this->name = $name;
        $this->hasDefault = $hasDefault;
        $this->default = $default;
        if ($type instanceof ReflectionNamedType)
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
     * Get 是否有默认值
     */
    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }
}
