<?php

namespace Imi\Util;

use Imi\App;
use Imi\Bean\Annotation;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanProxy;
use Imi\Bean\Parser\BeanParser;
use Imi\Bean\ReflectionContainer;
use Imi\Config;
use Imi\Main\Helper;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\MemoryTable;
use Imi\Tool\Tool;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Worker;
use Swoole\Process;

/**
 * 框架里杂七杂八的各种工具方法.
 */
abstract class Imi
{
    /**
     * 处理规则，暂只支持通配符*.
     *
     * @param string $rule
     *
     * @return string
     */
    public static function parseRule($rule)
    {
        return strtr(preg_quote($rule), [
            '/'     => '\/',
            '\\*'   => '.*',
        ]);
    }

    /**
     * 检查规则是否匹配，支持通配符*.
     *
     * @param string $rule
     * @param string $string
     *
     * @return bool
     */
    public static function checkRuleMatch($rule, $string)
    {
        $rule = '/^' . static::parseRule($rule) . '$/';

        return preg_match($rule, $string) > 0;
    }

    /**
     * 检查类和方法是否匹配，支持通配符*.
     *
     * @param string $rule
     * @param string $className
     * @param string $methodName
     *
     * @return bool
     */
    public static function checkClassMethodRule($rule, $className, $methodName)
    {
        list($classRule, $methodRule) = explode('::', $rule, 2);

        return static::checkRuleMatch($classRule, $className) && static::checkRuleMatch($methodRule, $methodName);
    }

    /**
     * 检查类是否匹配，支持通配符*.
     *
     * @param string $rule
     * @param string $className
     *
     * @return bool
     */
    public static function checkClassRule($rule, $className)
    {
        list($classRule) = explode('::', $rule, 2);

        return static::checkRuleMatch($classRule, $className);
    }

    /**
     * 检查验证比较规则集.
     *
     * @param string|array $rules
     * @param callable     $valueCallback
     *
     * @return bool
     */
    public static function checkCompareRules($rules, $valueCallback)
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
            elseif (preg_match('/^' . $rule . '$/', $valueCallback($fieldName)) <= 0)
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
     *
     * @param string   $rule
     * @param callable $valueCallback
     *
     * @return bool
     */
    public static function checkCompareRule($rule, $valueCallback)
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
     *
     * @return bool
     */
    public static function checkCompareValues($rules, $value)
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
     *
     * @return bool
     */
    public static function checkCompareValue($rule, $value)
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
     * @param string $rule
     *
     * @return string[]|false
     */
    public static function parseDotRule($rule)
    {
        $result = preg_split('#(?<!\\\)\.#', $rule);
        $result = str_replace('\.', '.', $result);

        return $result;
    }

    /**
     * 获取类命名空间.
     *
     * @param string $className
     *
     * @return string
     */
    public static function getClassNamespace(string $className)
    {
        return implode('\\', \array_slice(explode('\\', $className), 0, -1));
    }

    /**
     * 获取类短名称.
     *
     * @param string $className
     *
     * @return string
     */
    public static function getClassShortName(string $className)
    {
        return implode('', \array_slice(explode('\\', $className), -1));
    }

    /**
     * 根据命名空间获取真实路径，返回null则为获取失败.
     *
     * @param string $namespace
     *
     * @return string|null
     */
    public static function getNamespacePath($namespace)
    {
        if ('\\' !== substr($namespace, -1, 1))
        {
            $namespace .= '\\';
        }
        $loader = App::getLoader();
        if (null === $loader)
        {
            // Composer 加载器未赋值，则只能取Main类命名空间下的目录
            foreach (Helper::getMains() as $main)
            {
                $mainNamespace = $main->getNamespace();
                if ('\\' !== substr($mainNamespace, -1, 1))
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
                    break;
                }
            }
        }
        else
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
                    break;
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
     * @param string $namespace
     *
     * @return string[]
     */
    public static function getNamespacePaths($namespace): array
    {
        $resultPaths = [];
        if ('\\' !== substr($namespace, -1, 1))
        {
            $namespace .= '\\';
        }
        $loader = App::getLoader();
        if (null === $loader)
        {
            // Composer 加载器未赋值，则只能取Main类命名空间下的目录
            foreach (Helper::getMains() as $main)
            {
                $mainNamespace = $main->getNamespace();
                if ('\\' !== substr($mainNamespace, -1, 1))
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
        else
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
     * @param string $className
     * @param string $propertyName
     *
     * @return mixed
     */
    public static function getClassPropertyValue($className, $propertyName)
    {
        $value = BeanProxy::getInjectValue($className, $propertyName);
        if (null === $value)
        {
            if (!class_exists($className))
            {
                $className = BeanParser::getInstance()->getData()[$className]['className'];
            }
            $ref = ReflectionContainer::getClassReflection($className);
            $value = $ref->getDefaultProperties()[$propertyName] ?? null;
        }

        return $value;
    }

    /**
     * 获取imi命令行.
     *
     * @param string $toolName  工具名，如server
     * @param string $operation 操作名，如start
     * @param array  $args      参数
     *
     * @return string
     */
    public static function getImiCmd($toolName, $operation, $args = [])
    {
        $cmd = '"' . \PHP_BINARY . '" "' . App::get(ProcessAppContexts::SCRIPT_NAME) . '" ' . $toolName . '/' . $operation;
        if (null !== ($appNamespace = Args::get('appNamespace')))
        {
            $cmd .= ' -appNamespace "' . $appNamespace . '"';
        }
        if ($args)
        {
            foreach ($args as $k => $v)
            {
                if (is_numeric($k))
                {
                    $cmd .= ' -' . $v;
                }
                else
                {
                    $cmd .= ' -' . $k . ' "' . $v . '"';
                }
            }
        }

        return $cmd;
    }

    /**
     * 获取运行时目录路径.
     *
     * @param string ...$path
     *
     * @return string
     */
    public static function getRuntimePath(...$path)
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

        return File::path($parentPath, ...$path);
    }

    /**
     * 设置当前进程名.
     *
     * @param string $type
     * @param array  $data
     *
     * @return void
     */
    public static function setProcessName($type, $data = [])
    {
        if ('Darwin' === \PHP_OS)
        {
            // 苹果 MacOS 不允许设置进程名
            return;
        }
        cli_set_process_title(static::getProcessName($type, $data));
    }

    /**
     * 获取 imi 进程名
     * 返回false则失败.
     *
     * @param string $type
     * @param array  $data
     *
     * @return string|bool
     */
    public static function getProcessName($type, $data = [])
    {
        static $defaults = [
            'master'        => 'imi:master:{namespace}',
            'manager'       => 'imi:manager:{namespace}',
            'worker'        => 'imi:worker-{workerId}:{namespace}',
            'taskWorker'    => 'imi:taskWorker-{workerId}:{namespace}',
            'process'       => 'imi:process-{processName}:{namespace}',
            'processPool'   => 'imi:process-pool-{processPoolName}-{workerId}:{namespace}',
            'tool'          => 'imi:{toolName}/{toolOperation}:{namespace}',
        ];
        if (!isset($defaults[$type]))
        {
            return false;
        }
        $rule = Config::get('@app.process.' . $type, $defaults[$type]);
        $data['namespace'] = App::getNamespace();
        switch ($type)
        {
            case 'master':
                break;
            case 'manager':
                break;
            case 'worker':
                $data['workerId'] = Worker::getWorkerID();
                break;
            case 'taskWorker':
                $data['workerId'] = Worker::getWorkerID();
                break;
            case 'process':
                if (!isset($data['processName']))
                {
                    return false;
                }
                break;
            case 'processPool':
                if (!isset($data['processPoolName'], $data['workerId']))
                {
                    return false;
                }
                break;
            case 'tool':
                $data['toolName'] = Tool::getToolName();
                $data['toolOperation'] = Tool::getToolOperation();
                break;
        }
        $result = $rule;
        foreach ($data as $k => $v)
        {
            if (!is_scalar($v))
            {
                continue;
            }
            $result = str_replace('{' . $k . '}', $v, $result);
        }

        return $result;
    }

    /**
     * 构建运行时缓存.
     *
     * @param string $runtimeFile 如果为空则默认为runtime.cache
     *
     * @return void
     */
    public static function buildRuntime($runtimeFile = null)
    {
        /**
         * 处理列类型和大小.
         *
         * @param \Imi\Model\Annotation\Column $column
         *
         * @return array [$type, $size]
         */
        $parseColumnTypeAndSize = function ($column) {
            $type = $column->type;
            switch ($type)
            {
                case 'string':
                    $type = \Swoole\Table::TYPE_STRING;
                    $size = $column->length;
                    break;
                case 'int':
                    $type = \Swoole\Table::TYPE_INT;
                    $size = $column->length;
                    if (!\in_array($size, [1, 2, 4, 8]))
                    {
                        $size = 4;
                    }
                    break;
                case 'float':
                    $type = \Swoole\Table::TYPE_FLOAT;
                    $size = 8;
                    break;
                default:
                    $size = 0;
            }

            return [$type, $size];
        };

        /**
         * 获取内存表列.
         *
         * @param array $columnAnnotationsSet
         *
         * @return array
         */
        $getMemoryTableColumns = function ($columnAnnotationsSet) use ($parseColumnTypeAndSize) {
            $columns = [];

            foreach ($columnAnnotationsSet as $annotations)
            {
                $columnAnnotation = $annotations[0];
                list($type, $size) = $parseColumnTypeAndSize($columnAnnotation);
                $columns[] = [
                    'name' => $columnAnnotation->name,
                    'type' => $type,
                    'size' => $size,
                ];
            }

            return $columns;
        };

        $runtimeInfo = App::getRuntimeInfo();
        $annotationsSet = AnnotationManager::getAnnotationPoints(MemoryTable::class, 'class');
        foreach ($annotationsSet as &$item)
        {
            $item = clone $item;
            // @phpstan-ignore-next-line
            $item->columns = $getMemoryTableColumns(AnnotationManager::getPropertiesAnnotations($item->getClass(), Column::class)) ?? [];
        }
        $runtimeInfo->memoryTable = $annotationsSet;
        $runtimeInfo->annotationParserData = Annotation::getInstance()->getParser()->getStoreData();
        $runtimeInfo->annotationParserParsers = Annotation::getInstance()->getParser()->getParsers();
        $runtimeInfo->annotationManagerAnnotations = AnnotationManager::getAnnotations();
        $runtimeInfo->annotationManagerAnnotationRelation = AnnotationManager::getAnnotationRelation();
        $runtimeInfo->parsersData = [];
        foreach (array_unique($runtimeInfo->annotationParserParsers) as $parserClass)
        {
            $parser = $parserClass::getInstance();
            $runtimeInfo->parsersData[$parserClass] = $parser->getData();
        }
        if (null === $runtimeFile)
        {
            $runtimeFile = self::getRuntimePath('runtime.cache');
        }
        file_put_contents($runtimeFile, serialize($runtimeInfo));
    }

    /**
     * 增量更新运行时缓存.
     *
     * @param array $files
     *
     * @return void
     */
    public static function incrUpdateRuntime($files)
    {
        $parser = Annotation::getInstance()->getParser();
        $parser->parseIncr($files);

        foreach (App::getRuntimeInfo()->parsersData as $parserClass => $data)
        {
            // @phpstan-ignore-next-line
            $parserObject = $parserClass::getInstance();
            $parserObject->setData([]);
        }

        foreach ($parser->getClasses() as $className)
        {
            $parser->execParse($className);
        }
    }

    /**
     * 停止服务器.
     *
     * @return void
     */
    public static function stopServer()
    {
        $fileName = self::getRuntimePath(str_replace('\\', '-', App::getNamespace()) . '.pid');
        if (!is_file($fileName))
        {
            throw new \RuntimeException(sprintf('Pid file %s is not exists', $fileName));
        }
        $pid = json_decode(file_get_contents($fileName), true);
        if ($pid > 0)
        {
            Process::kill($pid['masterPID']);
        }
        else
        {
            throw new \RuntimeException(sprintf('Pid does not exists in file %s', $fileName));
        }
    }

    /**
     * 重新加载服务器.
     *
     * @return void
     */
    public static function reloadServer()
    {
        $fileName = self::getRuntimePath(str_replace('\\', '-', App::getNamespace()) . '.pid');
        if (!is_file($fileName))
        {
            throw new \RuntimeException(sprintf('Pid file %s is not exists', $fileName));
        }
        $pid = json_decode(file_get_contents($fileName), true);
        if ($pid > 0)
        {
            Process::kill($pid['masterPID'], \SIGUSR1);
        }
        else
        {
            throw new \RuntimeException(sprintf('Pid does not exists in file %s', $fileName));
        }
    }

    /**
     * 检查系统是否支持端口重用.
     *
     * @return bool
     */
    public static function checkReusePort()
    {
        return 'Linux' === \PHP_OS && version_compare(php_uname('r'), '3.9', '>=');
    }

    /**
     * eval 方法用的自增变量.
     *
     * @var int
     */
    private static $evalAtomic = 0;

    /**
     * eval() 函数的安全替代方法.
     *
     * @param string $code
     *
     * @return mixed
     */
    public static function eval(string $code)
    {
        $fileName = (is_dir('/run/shm') ? '/run/shm/' : '/tmp/') . 'imi-' . getmypid() . '-' . (++static::$evalAtomic) . '.php';
        $fp = fopen($fileName, 'x');
        if (false === $fp)
        {
            return eval($code);
        }
        else
        {
            try
            {
                if (!fwrite($fp, '<?php ' . $code))
                {
                    throw new \RuntimeException(sprintf('Unable to write temporary file: %s', $fileName));
                }
                fclose($fp);
                $closed = true;

                return require $fileName;
            }
            finally
            {
                if (!isset($closed))
                {
                    fclose($fp);
                }
                unlink($fileName);
            }
        }
    }

    /**
     * 检测是否为 WSL 环境.
     *
     * @return bool
     */
    public static function isWSL(): bool
    {
        return is_file('/mnt/c/Windows/explorer.exe');
    }

    /**
     * 获取 Linux 版本号.
     *
     * @return string
     */
    public static function getLinuxVersion(): string
    {
        if (preg_match_all('/^((NAME="?(?<name>.+)"?)|VERSION="?(?<version>.+)"?)/im', `cat /etc/*-release`, $matches) <= 0)
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
     *
     * @return string
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
     *
     * @return string
     */
    public static function getCygwinVersion(): string
    {
        if (preg_match('/^cygwin\s+(\S+)\s+OK$/', exec('cygcheck -c cygwin'), $matches) > 0)
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
     *
     * @return bool
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
}
