<?php

declare(strict_types=1);

namespace Imi\Main;

use Imi\Bean\ReflectionContainer;

/**
 * 主类基类.
 */
abstract class BaseMain implements IMain
{
    /**
     * 当前模块根路径.
     */
    protected ?string $path = null;

    /**
     * 当前模块命名空间.
     */
    protected ?string $namespace = null;

    /**
     * 模块名称.
     */
    protected string $moduleName = '';

    /**
     * 模块配置.
     */
    protected ?array $config = null;

    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
        $this->__init();
    }

    /**
     * 获取当前模块根路径.
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
     */
    public function getBeanScan(): array
    {
        return $this->getConfig()['beanScan'] ?? [];
    }

    /**
     * 获取配置.
     */
    public function getConfig(): array
    {
        if (null === $this->config)
        {
            $fileName = $this->getPath() . '/config/config.php';
            if (is_file($fileName))
            {
                return $this->config = include $fileName;
            }
            else
            {
                return $this->config = [];
            }
        }

        return $this->config;
    }

    /**
     * 获取模块名称.
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }
}
