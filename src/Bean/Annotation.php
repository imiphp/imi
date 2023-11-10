<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Config;
use Imi\Main\Helper as MainHelper;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Traits\TSingleton;

/**
 * 注解处理类.
 */
class Annotation
{
    use TSingleton;

    /**
     * 加载器.
     */
    private ?AnnotationLoader $loader = null;

    /**
     * 处理器.
     */
    private ?AnnotationParser $parser = null;

    public function __construct()
    {
        $this->loader = new AnnotationLoader();
        $this->parser = new AnnotationParser();
    }

    /**
     * 初始化.
     *
     * @param \Imi\Main\BaseMain[]|null $mains
     */
    public function init(?array $mains = null): void
    {
        if (null === $mains)
        {
            $mains = MainHelper::getMains();
        }
        foreach ($mains as $main)
        {
            // 扫描注解
            $this->loadModuleAnnotations($main->getNamespace());
        }
    }

    /**
     * 初始化.
     *
     * @param string|string[] $namespaces
     */
    public function initByNamespace(string|array $namespaces, bool $isApp = false): void
    {
        foreach ((array) $namespaces as $namespace)
        {
            $this->loadModuleAnnotations($namespace, $isApp);
        }
    }

    /**
     * 获取加载器.
     */
    public function getLoader(): AnnotationLoader
    {
        return $this->loader;
    }

    /**
     * 获取处理器.
     */
    public function getParser(): AnnotationParser
    {
        return $this->parser;
    }

    /**
     * 加载模块注解.
     */
    private function loadModuleAnnotations(string $namespace, bool $isApp = false): void
    {
        $appConfig = Config::get('@app');
        // 默认过滤的命名空间
        $ignoredNamespaces = ($appConfig['overrideDefaultIgnoreNamespace'] ?? false) ? [] : [
            $namespace . '\\config',
            $namespace . '\\vendor',
        ];
        $ignoredPaths = [Imi::getRuntimePath()];
        if (!($appConfig['overrideDefaultIgnorePaths'] ?? false))
        {
            foreach (Imi::getNamespacePaths($namespace) as $path)
            {
                $ignoredPaths[] = File::path($path, '.git');
                $ignoredPaths[] = File::path($path, 'config');
                $ignoredPaths[] = File::path($path, 'vendor');
            }
        }
        foreach (Config::getAliases() as $alias)
        {
            $config = Config::get($alias);
            $ignoredNamespaces = array_merge($ignoredNamespaces, $config['ignoreNamespace'] ?? []);
            $ignoredPaths = array_merge($ignoredPaths, $config['ignorePaths'] ?? []);
            if ('@app' === $alias && !$isApp)
            {
                continue;
            }
            $ignoredPaths = array_merge($ignoredPaths, $config['appIgnorePaths'] ?? []);
        }
        $ignoredPaths = array_map(Imi::parseRule(...), $ignoredPaths);
        $pathPattern = '/^(?!((' . implode(')|(', $ignoredPaths) . ')))/';
        if ($ignoredNamespaces)
        {
            $list = [];
            foreach ($ignoredNamespaces as $ns)
            {
                $list[] = Imi::parseRule($ns);
            }
            $pattern = '/^((' . implode(')|(', $list) . '))$/';
        }
        else
        {
            $pattern = null;
        }
        $parser = $this->parser;
        $this->loader->loadModuleAnnotations($namespace, static function (string $fileNamespace, string $fileName) use ($pattern, $parser): void {
            if ($pattern && 1 === preg_match($pattern, $fileNamespace))
            {
                return;
            }
            if (!$parser->isParsed($fileNamespace) && $parser->parse($fileNamespace, true, $fileName))
            {
                $parser->execParse($fileNamespace);
            }
        }, $pathPattern);
    }
}
