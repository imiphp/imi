<?php
namespace Imi\Core\Contract;

abstract class BaseApp implements IApp
{
    /**
     * 命名空间
     * @var string
     */
    protected string $namespace;

    /**
     * 构造方法
     *
     * @param string $namespace
     * @return void
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

}
