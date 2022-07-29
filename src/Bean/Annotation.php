<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Config;
use Imi\Main\Helper as MainHelper;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Traits\TSingleton;
use ReflectionClass;

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
    public function initByNamespace($namespaces, bool $isApp = false): void
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
        $ignoredPaths = array_map([Imi::class, 'parseRule'], $ignoredPaths);
        if ($ignoredPaths)
        {
            $pathPattern = '/^(?!((' . implode(')|(', $ignoredPaths) . ')))/';
        }
        else
        {
            $pathPattern = '/^.+\.php$/i';
        }
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
        $this->loader->loadModuleAnnotations($namespace, static function (string $fileNamespace, string $fileName) use ($pattern, $parser) {
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

    /**
     * 注解类转注释文本.
     *
     * @param \Imi\Bean\Annotation\Base $annotation
     * @param bool                      $skipDefaultValue 过滤默认值不显示
     */
    public static function toComments(Annotation\Base $annotation, bool $skipDefaultValue = true): string
    {
        $result = '@' . Imi::getClassShortName(\get_class($annotation));
        $properties = [];
        if ($skipDefaultValue)
        {
            $refClass = new ReflectionClass($annotation);
            $defaultProperties = $refClass->getDefaultProperties();
        }
        else
        {
            $defaultProperties = null;
        }
        foreach ($annotation as $k => $v)
        {
            if ($skipDefaultValue && $v === ($defaultProperties[$k] ?? null))
            {
                continue;
            }
            if (\is_string($v))
            {
                $value = '"' . $v . '"';
            }
            else
            {
                $value = json_encode($v, \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
                if (\is_array($v))
                {
                    $value = '{' . substr($value, 1, -1) . '}';
                }
            }
            $properties[] = $k . '=' . $value;
        }
        if ($properties)
        {
            $result .= '(' . implode(', ', $properties) . ')';
        }

        return $result;
    }
}
