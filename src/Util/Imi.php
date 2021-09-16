<?php

declare(strict_types=1);

namespace Imi\Util;

use Composer\InstalledVersions;
use Imi\App;
use Imi\Bean\Annotation;
use Imi\Bean\BeanManager;
use Imi\Bean\BeanProxy;
use Imi\Bean\ReflectionContainer;
use Imi\Config;
use Imi\Event\Event;
use Imi\Main\Helper;
use Imi\Util\Process\ProcessAppContexts;

/**
 * 框架里杂七杂八的各种工具方法.
 */
class Imi
{
    private function __construct()
    {
    }

    /**
     * 处理规则，暂只支持通配符*.
     */
    public static function parseRule(string $rule): string
    {
        return strtr(preg_quote($rule), [
            '/'     => '\/',
            '\\*'   => '.*',
        ]);
    }

    /**
     * 检查规则是否匹配，支持通配符*.
     */
    public static function checkRuleMatch(string $rule, string $string): bool
    {
        $rule = '/^' . static::parseRule($rule) . '$/';

        return preg_match($rule, $string) > 0;
    }

    /**
     * 检查类和方法是否匹配，支持通配符*.
     */
    public static function checkClassMethodRule(string $rule, string $className, string $methodName): bool
    {
        list($classRule, $methodRule) = explode('::', $rule, 2);

        return static::checkRuleMatch($classRule, $className) && static::checkRuleMatch($methodRule, $methodName);
    }

    /**
     * 检查类是否匹配，支持通配符*.
     */
    public static function checkClassRule(string $rule, string $className): bool
    {
        list($classRule) = explode('::', $rule, 2);

        return static::checkRuleMatch($classRule, $className);
    }

    /**
     * 检查验证比较规则集.
     *
     * @param string|array $rules
     */
    public static function checkCompareRules($rules, callable $valueCallback): bool
    {
        foreach ((array) $rules as $fieldName => $rule)
        {
            if (is_numeric($fieldName))
            {
                if (!static::checkCompareRule($rule, $valueCallback))
                {
                    return false;
                }
            }
            elseif (preg_match('/^' . $rule . '$/', (string) $valueCallback($fieldName)) <= 0)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 检查验证比较规则，如果符合规则返回bool，不符合规则返回null
     * id=1
     * id!=1 id<>1
     * id
     * !id.
     */
    public static function checkCompareRule(string $rule, callable $valueCallback): bool
    {
        if (isset($rule[0]) && '!' === $rule[0])
        {
            // 不应该存在参数支持
            return null === $valueCallback(substr($rule, 1));
        }
        elseif (preg_match('/([^!<=]+)(!=|<>|=)(.+)/', $rule, $matches) > 0)
        {
            $value = $valueCallback($matches[1]);
            switch ($matches[2])
            {
                case '!=':
                case '<>':
                    return null !== $value && $value != $matches[3];
                case '=':
                    return $value == $matches[3];
                default:
                    return false;
            }
        }
        else
        {
            return null !== $valueCallback($rule);
        }
    }

    /**
     * 检查验证比较值集.
     *
     * @param string|array $rules
     * @param mixed        $value
     */
    public static function checkCompareValues($rules, $value): bool
    {
        foreach ((array) $rules as $rule)
        {
            if (!static::checkCompareValue($rule, $value))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 检查验证比较值
     *
     * @param string|array $rule
     * @param mixed        $value
     */
    public static function checkCompareValue($rule, $value): bool
    {
        if (isset($rule[0]) && '!' === $rule[0])
        {
            // 不等
            return $value !== substr($rule, 1);
        }
        else
        {
            // 相等
            return $value === $rule;
        }
    }

    /**
     * 处理按.分隔的规则文本，支持\.转义不分隔.
     *
     * @return string[]
     */
    public static function parseDotRule(string $rule): array
    {
        $result = preg_split('#(?<!\\\)\.#', $rule);
        $result = str_replace('\.', '.', $result);

        return $result;
    }

    /**
     * 获取类命名空间.
     */
    public static function getClassNamespace(string $className): string
    {
        return implode('\\', \array_slice(explode('\\', $className), 0, -1));
    }

    /**
     * 获取类短名称.
     */
    public static function getClassShortName(string $className): string
    {
        return implode('', \array_slice(explode('\\', $className), -1));
    }

    /**
     * 根据命名空间获取真实路径，返回null则为获取失败.
     */
    public static function getNamespacePath(string $namespace): ?string
    {
        if ('\\' !== ($namespace[-1] ?? ''))
        {
            $namespace .= '\\';
        }
        $loaders = Composer::getClassLoaders();
        if ($loaders)
        {
            foreach ($loaders as $loader)
            {
                // 依靠 Composer PSR-4 配置的目录进行定位目录
                $prefixDirsPsr4 = $loader->getPrefixesPsr4();
                foreach ($prefixDirsPsr4 as $keyNamespace => $paths)
                {
                    $len = \strlen($keyNamespace);
                    if (substr($namespace, 0, $len) === $keyNamespace)
                    {
                        if (isset($paths[1]))
                        {
                            return null;
                        }
                        $result = File::path($paths[0], str_replace('\\', \DIRECTORY_SEPARATOR, substr($namespace, $len)));
                        if (is_dir($result))
                        {
                            break 2;
                        }
                    }
                }
            }
        }
        else
        {
            // Composer 加载器未赋值，则只能取Main类命名空间下的目录
            foreach (Helper::getMains() as $main)
            {
                $mainNamespace = $main->getNamespace();
                if ('\\' !== ($mainNamespace[-1] ?? ''))
                {
                    $mainNamespace .= '\\';
                }
                $len = \strlen($mainNamespace);
                if ($mainNamespace === substr($namespace, 0, $len))
                {
                    $namespaceSubPath = substr($namespace, $len);
                    $refClass = ReflectionContainer::getClassReflection(\get_class($main));
                    $path = \dirname($refClass->getFileName());
                    $result = File::path($path, str_replace('\\', \DIRECTORY_SEPARATOR, $namespaceSubPath));
                    if (is_dir($result))
                    {
                        break;
                    }
                }
            }
        }
        if (isset($result))
        {
            return File::absolute($result);
        }

        return null;
    }

    /**
     * 根据命名空间获取真实路径，允许返回多个.
     *
     * @return string[]
     */
    public static function getNamespacePaths(string $namespace): array
    {
        $resultPaths = [];
        if ('\\' !== ($namespace[-1] ?? ''))
        {
            $namespace .= '\\';
        }
        $loaders = Composer::getClassLoaders();
        if ($loaders)
        {
            foreach ($loaders as $loader)
            {
                // 依靠 Composer PSR-4 配置的目录进行定位目录
                $prefixDirsPsr4 = $loader->getPrefixesPsr4();
                foreach ($prefixDirsPsr4 as $keyNamespace => $paths)
                {
                    $len = \strlen($keyNamespace);
                    if (substr($namespace, 0, $len) === $keyNamespace)
                    {
                        foreach ($paths as $path)
                        {
                            $resultPaths[] = File::path($path, str_replace('\\', \DIRECTORY_SEPARATOR, substr($namespace, $len)));
                        }
                    }
                }
            }
        }
        else
        {
            // Composer 加载器未赋值，则只能取Main类命名空间下的目录
            foreach (Helper::getMains() as $main)
            {
                $mainNamespace = $main->getNamespace();
                if ('\\' !== ($mainNamespace[-1] ?? ''))
                {
                    $mainNamespace .= '\\';
                }
                $len = \strlen($mainNamespace);
                if ($mainNamespace === substr($namespace, 0, $len))
                {
                    $namespaceSubPath = substr($namespace, $len);
                    $refClass = ReflectionContainer::getClassReflection(\get_class($main));
                    $path = \dirname($refClass->getFileName());
                    $resultPaths[] = File::path($path, str_replace('\\', \DIRECTORY_SEPARATOR, $namespaceSubPath));
                }
            }
        }
        $resultPaths = array_unique($resultPaths);
        foreach ($resultPaths as &$path)
        {
            $path = File::absolute($path);
        }

        return array_unique($resultPaths);
    }

    /**
     * 获取类属性的值，值为beans配置或默认配置，支持传入Bean名称
     * 构造方法赋值无法取出.
     *
     * @return mixed
     */
    public static function getClassPropertyValue(string $className, string $propertyName)
    {
        $value = BeanProxy::getInjectValue($className, $propertyName);
        if (null === $value)
        {
            if (!class_exists($className))
            {
                $data = BeanManager::get($className);
                $className = $data['className'] ?? null;
            }
            $ref = ReflectionContainer::getClassReflection($className);
            $value = $ref->getDefaultProperties()[$propertyName] ?? null;
        }

        return $value;
    }

    /**
     * 获取imi命令行.
     */
    public static function getImiCmd(string $commandName, array $arguments = [], array $options = []): string
    {
        $cmd = '"' . \PHP_BINARY . '" "' . App::get(ProcessAppContexts::SCRIPT_NAME) . '" ' . $commandName;
        $options['app-namespace'] ??= App::getNamespace();
        if ($arguments)
        {
            foreach ($arguments as $v)
            {
                $cmd .= ' "' . $v . '"';
            }
        }
        foreach ($options as $k => $v)
        {
            if (is_numeric($k))
            {
                $cmd .= ' -' . (isset($v[1]) ? '-' : '') . $v;
            }
            else
            {
                $cmd .= ' -' . (isset($k[1]) ? '-' : '') . $k . ' "' . $v . '"';
            }
        }

        return $cmd;
    }

    /**
     * 运行时目录路径.
     *
     * @return string
     */
    private static string $runtimePath = '';

    /**
     * 获取运行时目录路径.
     *
     * @param string ...$path
     */
    public static function getRuntimePath(string ...$path): string
    {
        if ('' === static::$runtimePath)
        {
            $parentPath = Config::get('@app.runtimePath');
            if (null === $parentPath)
            {
                $namespacePaths = self::getNamespacePaths($namespace = App::getNamespace());
                $resultNamespacePath = null;
                foreach ($namespacePaths as $namespacePath)
                {
                    if (is_dir($namespacePath))
                    {
                        $resultNamespacePath = $namespacePath;
                        break;
                    }
                }
                if (null === $resultNamespacePath)
                {
                    throw new \RuntimeException(sprintf('Cannot found path of namespace %s. You can set the config @app.runtimePath.', $namespace));
                }
                $parentPath = File::path($resultNamespacePath, '.runtime');
            }
            File::createDir($parentPath);
            static::$runtimePath = $parentPath;
        }

        return File::path(static::$runtimePath, ...$path);
    }

    /**
     * 构建运行时缓存.
     *
     * @param string|null $cacheName 如果为空则默认为runtime
     */
    public static function buildRuntime(?string $cacheName = null): void
    {
        if (null === $cacheName)
        {
            $cacheName = self::getRuntimePath('runtime');
        }
        if (!is_dir($cacheName))
        {
            mkdir($cacheName, 0775, true);
        }
        $data = [];
        $data['__imi_ver'] = InstalledVersions::getReference('imiphp/imi');
        Event::trigger('IMI.BUILD_RUNTIME', [
            'cacheName' => $cacheName,
            'data'      => &$data,
        ]);
        file_put_contents(File::path($cacheName, 'runtime.cache'), serialize($data));
    }

    /**
     * 从文件加载运行时数据.
     */
    public static function loadRuntimeInfo(string $cacheName, bool $onlyImi = false): bool
    {
        $fileName = File::path($cacheName, 'runtime.cache');
        if (!is_file($fileName))
        {
            return false;
        }
        $data = unserialize(file_get_contents($fileName));
        if (!$data)
        {
            return false;
        }
        if (isset($data['__imi_ver']) && $data['__imi_ver'] !== InstalledVersions::getReference('imiphp/imi'))
        {
            return false;
        }
        $success = true;
        Event::trigger('IMI.LOAD_RUNTIME_INFO', [
            'cacheName' => $cacheName,
            'data'      => $data,
            'success'   => &$success,
            'onlyImi'   => $onlyImi,
        ]);

        return $success;
    }

    /**
     * 增量更新运行时缓存.
     */
    public static function incrUpdateRuntime(array $files): void
    {
        $parser = Annotation::getInstance()->getParser();
        $parser->parseIncr($files);

        foreach ($parser->getClasses() as $className)
        {
            $parser->execParse($className);
        }
    }

    /**
     * 检查系统是否支持端口重用.
     */
    public static function checkReusePort(): bool
    {
        return 'Linux' === \PHP_OS && version_compare(php_uname('r'), '3.9', '>=');
    }

    /**
     * eval 方法用的自增变量.
     */
    private static int $evalAtomic = 0;

    /**
     * 临时目录地址
     */
    private static string $tmpPath = '';

    /**
     * eval() 函数的安全替代方法.
     *
     * @return mixed
     */
    public static function eval(string $code, ?string $fileName = null, bool $deleteFile = true)
    {
        $tmpPath = &static::$tmpPath;
        if ('' === $tmpPath)
        {
            if (is_dir('/run/shm'))
            {
                $tmpPath = '/run/shm';
            }
            elseif (is_dir('/tmp'))
            {
                $tmpPath = '/tmp';
            }
            else
            {
                $tmpPath = sys_get_temp_dir();
            }
        }
        if (null === $fileName)
        {
            $fileName = $tmpPath . '/' . 'imi-' . getmypid() . '-' . (++static::$evalAtomic) . '.php';
        }

        if (false === file_put_contents($fileName, '<?php ' . $code))
        {
            return eval($code);
        }
        else
        {
            if ($deleteFile)
            {
                try
                {
                    return require $fileName;
                }
                finally
                {
                    unlink($fileName);
                }
            }
            else
            {
                return require $fileName;
            }
        }
    }

    /**
     * 检测是否为 WSL 环境.
     */
    public static function isWSL(): bool
    {
        return is_file('/mnt/c/Windows/explorer.exe');
    }

    /**
     * 获取 Linux 版本号.
     */
    public static function getLinuxVersion(): string
    {
        if (preg_match_all('/^((NAME="?(?<name>.+)"?)|VERSION="?(?<version>.+)"?)/im', shell_exec('cat /etc/*-release'), $matches) <= 0)
        {
            return '';
        }
        if (!isset($matches['name']))
        {
            return '';
        }
        $name = '';
        foreach ($matches['name'] as $name)
        {
            if ('' !== $name)
            {
                break;
            }
        }
        $result = trim($name, '"');
        if (isset($matches['version']))
        {
            $version = '';
            foreach ($matches['version'] as $version)
            {
                if ('' !== $version)
                {
                    break;
                }
            }
            if ('' !== $version)
            {
                $result .= ' ' . trim($version, '"');
            }
        }

        return $result;
    }

    /**
     * 获取苹果系统版本.
     */
    public static function getDarwinVersion(): string
    {
        $xml = simplexml_load_file('/System/Library/CoreServices/SystemVersion.plist');
        if (!$xml)
        {
            return '';
        }
        $i = 0;
        foreach ($xml->dict->key as $item)
        {
            switch ($item->__toString())
            {
                case 'ProductName':
                    $name = $xml->dict->string[$i]->__toString();
                    break;
                case 'ProductUserVisibleVersion':
                    $version = $xml->dict->string[$i]->__toString();
                    break;
            }
            ++$i;
        }
        if (!isset($name))
        {
            return '';
        }
        $result = $name;
        if (isset($version))
        {
            $result .= ' ' . $version;
        }

        return $result;
    }

    /**
     * 获取 Cygwin 版本.
     */
    public static function getCygwinVersion(): string
    {
        if (preg_match('/^cygwin\s+(\S+)\s+OK$/', exec('which cygcheck && cygcheck -c cygwin'), $matches) > 0)
        {
            return $matches[1];
        }
        else
        {
            return '';
        }
    }

    /**
     * 判断是否为 Docker 环境.
     */
    public static function isDockerEnvironment(): bool
    {
        $fileName = '/proc/1/cgroup';
        if (is_file($fileName))
        {
            return false !== strpos(file_get_contents($fileName), ':/docker/');
        }

        return false;
    }

    /**
     * 检查应用运行类型.
     */
    public static function checkAppType(string $appType): bool
    {
        return App::isInited() && $appType === App::getApp()->getType();
    }
}
