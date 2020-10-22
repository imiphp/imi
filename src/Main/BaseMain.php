<?php

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
     * @var string
     */
    protected $path;

    /**
     * 当前模块命名空间.
     *
     * @var string
     */
    protected $namespace;

    /**
     * 模块名称.
     *
     * @var string
     */
    protected $moduleName;

    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
        // $this->loadConfig();
        // $this->loadComponents();
        $this->__init();
    }

    /**
     * 加载配置.
     *
     * @return void
     */
    public function loadConfig()
    {
        $fileName = $this->getPath() . \DIRECTORY_SEPARATOR . 'config/config.php';
        if (is_file($fileName))
        {
            $name = '@' . $this->moduleName;
            Config::removeConfig($name);
            Config::addConfig($name, include $fileName);
        }
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
    public function getConfig()
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

    /**
     * 加载组件.
     *
     * @return void
     */
    public function loadComponents()
    {
        foreach (Config::get('@' . $this->moduleName . '.components', []) as $componentName => $namespace)
        {
            if (!Helper::getMain($namespace, $componentName))
            {
                throw new \RuntimeException(sprintf('Component [%s] must have the class %s\\Main', $componentName, $namespace));
            }
        }
    }
}
