<?php

namespace Imi\Server\Http\Struct;

class ActionMethodItem
{
    /**
     * 参数名.
     *
     * @var string
     */
    protected string $name;

    /**
     * 默认值
     *
     * @var mixed
     */
    protected $default;

    public function __construct(string $name, $default)
    {
        $this->name = $name;
        $this->default = $default;
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
}
