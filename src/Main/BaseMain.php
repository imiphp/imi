<?php

declare(strict_types=1);

namespace Imi\Main;

use Imi\Bean\ReflectionContainer;
use Imi\Config;

/**
 * 主类基类.
 */
abstract class BaseMain implements IMain
{
    /**
     * 当前模块根路径.
     *
     * @var string|null
     */
    protected ?string $path = null;

    /**
     * 当前模块命名空间.
     *
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * 模块名称.
     *
     * @var string
     */
    protected string $moduleName = '';

    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
        $this->__init();
    }

    /**
     * 获取当前模块根路径.
     *
     * @return string
     */
    public function getPath(): string
    {
        if (null === $this->path)
        {
            $ref = ReflectionContainer::getClassReflection(static::class);
            $this->path = \dirname($ref->getFileName());
        }

        return $this->path;
    }

    /**
     * 获取当前模块命名空间.
     *
     * @return string
     */
    public function getNamespace(): string
    {
        if (null === $this->namespace)
        {
            $this->namespace = str_replace(\DIRECTORY_SEPARATOR, '\\', \dirname(str_replace('\\', \DIRECTORY_SEPARATOR, static::class)));
        }

        return $this->namespace;
    }

    /**
     * 获取要扫描的bean的命名空间.
     *
     * @return array
     */
    public function getBeanScan(): array
    {
        return Config::get('@' . $this->moduleName . '.beanScan', []);
    }

    /**
     * 获取配置.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return Config::get('@' . $this->moduleName, []);
    }

    /**
     * 获取模块名称.
     *
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }
}
