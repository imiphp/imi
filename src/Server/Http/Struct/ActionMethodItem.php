<?php

declare(strict_types=1);

namespace Imi\Server\Http\Struct;

use Psr\Http\Message\UploadedFileInterface;

class ActionMethodItem
{
    /**
     * 内部类型-通用.
     */
    public const TYPE_COMMON = 1;

    /**
     * 内部类型-枚举.
     */
    public const TYPE_UNIT_ENUM = 2;

    /**
     * 内部类型-回退枚举.
     */
    public const TYPE_BACKED_ENUM = 3;

    /**
     * 内部类型-上传文件.
     */
    public const TYPE_UPLOADED_FILE = 4;

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
     * 类型数组.
     *
     * @var array{name: string, type: int, enumBackingType: string|null}[]
     */
    protected array $types = [];

    /**
     * @param mixed $default
     */
    public function __construct(string $name, bool $hasDefault, $default, bool $allowNull, ?\ReflectionType $type)
    {
        $this->name = $name;
        $this->hasDefault = $hasDefault;
        $this->default = $default;
        $this->allowNull = $allowNull;
        if ($type)
        {
            $this->parseTypes($type);
        }
    }

    private function parseTypes(\ReflectionType $type): void
    {
        if ($type instanceof \ReflectionNamedType)
        {
            if (is_subclass_of($typeClass = $type->getName(), \UnitEnum::class))
            {
                if (is_subclass_of($typeClass, \BackedEnum::class))
                {
                    $type = self::TYPE_BACKED_ENUM;
                    $reflectionEnum = new \ReflectionEnum($typeClass);
                    $backingType = $reflectionEnum->getBackingType();
                    if ($backingType)
                    {
                        $enumBackingType = $backingType->getName();
                    }
                }
                else
                {
                    $type = self::TYPE_UNIT_ENUM;
                }
                $this->types[] = [
                    'name'            => $typeClass,
                    'type'            => $type,
                    'enumBackingType' => $enumBackingType ?? null,
                ];
            }
            else
            {
                $type = $type->getName();
                if (UploadedFileInterface::class === $type || is_subclass_of($type, UploadedFileInterface::class))
                {
                    $type = self::TYPE_UPLOADED_FILE;
                }
                else
                {
                    $type = self::TYPE_COMMON;
                }
                $this->types[] = [
                    'name'            => $typeClass,
                    'type'            => $type,
                    'enumBackingType' => null,
                ];
            }
        }
        elseif ($type instanceof \ReflectionUnionType)
        {
            foreach ($type->getTypes() as $type)
            {
                if ($type instanceof \ReflectionNamedType)
                {
                    $this->parseTypes($type);
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
     * Get 类型数组.
     *
     * @return array{name: string, type: int, enumBackingType: string|null}[]
     */
    public function getTypes(): array
    {
        return $this->types;
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
