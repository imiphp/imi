<?php

declare(strict_types=1);

namespace Imi\Server\Http\Struct;

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
    protected $default = null;

    /**
     * 是否允许为 null.
     */
    protected bool $allowNull = false;

    /**
     * 类型.
     */
    protected ?string $type = null;

    /**
     * 类型类名.
     */
    protected ?string $typeClass = null;

    /**
     * @param mixed $default
     */
    public function __construct(string $name, bool $hasDefault, $default, bool $allowNull, ?\ReflectionType $type)
    {
        $this->name = $name;
        $this->hasDefault = $hasDefault;
        $this->default = $default;
        $this->allowNull = $allowNull;
        if ($type instanceof \ReflectionNamedType)
        {
            if (is_subclass_of($typeClass = $type->getName(), \UnitEnum::class))
            {
                $this->typeClass = $typeClass;
                if (is_subclass_of($typeClass, \BackedEnum::class))
                {
                    $this->type = \BackedEnum::class;
                }
                else
                {
                    $this->type = \UnitEnum::class;
                }
            }
            else
            {
                $this->type = $type->getName();
            }
        }
        elseif ($type instanceof \ReflectionUnionType)
        {
            foreach ($type->getTypes() as $type)
            {
                if (is_subclass_of($typeClass = $type->getName(), \UnitEnum::class))
                {
                    $this->typeClass = $typeClass;
                    if (is_subclass_of($typeClass, \BackedEnum::class))
                    {
                        $this->type = \BackedEnum::class;
                    }
                    else
                    {
                        $this->type = \UnitEnum::class;
                    }
                    break;
                }
            }
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

    /**
     * 获取类型类名.
     */
    public function getTypeClass(): ?string
    {
        return $this->typeClass;
    }
}
