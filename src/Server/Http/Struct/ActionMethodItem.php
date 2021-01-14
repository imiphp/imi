<?php

declare(strict_types=1);

namespace Imi\Server\Http\Struct;

class ActionMethodItem
{
    /**
     * 参数名.
     *
     * @var string
     */
    protected string $name = '';

    /**
     * 默认值
     *
     * @var mixed
     */
    protected $default;

    /**
     * 类型.
     *
     * @var string|null
     */
    protected ?string $type = null;

    public function __construct(string $name, $default, ?\ReflectionType $type)
    {
        $this->name = $name;
        $this->default = $default;
        if ($type)
        {
            $this->type = $type->getName();
        }
    }

    /**
     * Get 参数名.
     *
     * @return string
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
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
}
