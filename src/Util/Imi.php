<?php
namespace Imi\Util;

use Imi\App;
use Imi\Config;
use Imi\Worker;
use Imi\Util\Args;
use Imi\Main\Helper;
use Imi\Bean\BeanProxy;
use Imi\Bean\Parser\BeanParser;

/**
 * 框架里杂七杂八的各种工具方法
 */
abstract class Imi
{
    /**
     * 处理规则，暂只支持通配符*
     * @param string $rule
     * @return string
     */
    public static function parseRule($rule)
    {
        return \str_replace('/', '\/', \str_replace('\\*', '.*', \preg_quote($rule)));
    }

    /**
     * 检查规则是否匹配，支持通配符*
     * @param string $rule
     * @param string $string
     * @return boolean
     */
    public static function checkRuleMatch($rule, $string)
    {
        $rule = '/^' . static::parseRule($rule) . '$/';
        return \preg_match($rule, $string) > 0;
    }

    /**
     * 检查类和方法是否匹配，支持通配符*
     * @param string $rule
     * @param string $className
     * @param string $methodName
     * @return boolean
     */
    public static function checkClassMethodRule($rule, $className, $methodName)
    {
        list($classRule, $methodRule) = explode('::', $rule, 2);
        return static::checkRuleMatch($classRule, $className) && static::checkRuleMatch($methodRule, $methodName);
    }

    /**
     * 检查类是否匹配，支持通配符*
     * @param string $rule
     * @param string $className
     * @return boolean
     */
    public static function checkClassRule($rule, $className)
    {
        list($classRule, ) = explode('::', $rule, 2);
        return static::checkRuleMatch($classRule, $className);
    }

    /**
     * 检查验证比较规则集
     * @param string|array $rules
     * @param callable $valueCallback
     * @return boolean
     */
    public static function checkCompareRules($rules, $valueCallback)
    {
        foreach(is_array($rules) ? $rules : [$rules] as $fieldName => $rule)
        {
            if(is_numeric($fieldName))
            {
                if(!static::checkCompareRule($rule, $valueCallback))
                {
                    return false;
                }
            }
            else if(preg_match('/^' . $rule . '$/', call_user_func($valueCallback, $fieldName)) <= 0)
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
     * !id
     * @param string $rule
     * @param callable $valueCallback
     * @return boolean
     */
    public static function checkCompareRule($rule, $valueCallback)
    {
        if(isset($rule[0]) && '!' === $rule[0])
        {
            // 不应该存在参数支持
            return null === call_user_func($valueCallback, substr($rule, 1));
        }
        else if(preg_match('/([^!<=]+)(!=|<>|=)(.+)/', $rule, $matches) > 0)
        {
            $value = call_user_func($valueCallback, $matches[1]);
            switch($matches[2])
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
            return null !== call_user_func($valueCallback, $rule);
        }
    }

    /**
     * 检查验证比较值集
     * @param string|array $rules
     * @param mixed $value
     * @return boolean
     */
    public static function checkCompareValues($rules, $value)
    {
        foreach(is_array($rules) ? $rules : [$rules] as $rule)
        {
            if(!static::checkCompareValue($rule, $value))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查验证比较值
     * @param string|array $rule
     * @param mixed $value
     * @return boolean
     */
    public static function checkCompareValue($rule, $value)
    {
        if(isset($rule[0]) && '!' === $rule[0])
        {
            // 不等
            return $value !== $rule;
        }
        else
        {
            // 相等
            return $value === $rule;
        }
    }

    /**
     * 处理按.分隔的规则文本，支持\.转义不分隔
     * @param string $rule
     */
    public static function parseDotRule($rule)
    {
        $result = preg_split('#(?<!\\\)\.#', $rule);
        array_walk($result, function(&$value, $key){
            if(false !== strpos($value,'\.'))
            {
                $value = str_replace('\.', '.', $value);
            }
        });
        return $result;
    }

    /**
     * 获取类命名空间
     * @param string $className
     * @return string
     */
    public static function getClassNamespace(string $className)
    {
        return implode('\\', array_slice(explode('\\', $className), 0, -1));
    }

    /**
     * 获取类短名称
     * @param string $className
     * @return string
     */
    public static function getClassShortName(string $className)
    {
        return implode('', array_slice(explode('\\', $className), -1));
    }

    /**
     * 根据命名空间获取真实路径，返回null则为获取失败
     * @param string $namespace
     * @return string|null
     */
    public static function getNamespacePath($namespace)
    {
        if('\\' !== substr($namespace, -1, 1))
        {
            $namespace .= '\\';
        }
        $loader = App::getLoader();
        if(null === $loader)
        {
            // Composer 加载器未赋值，则只能取Main类命名空间下的目录
            foreach(Helper::getMains() as $main)
            {
                $mainNamespace = $main->getNamespace();
                if('\\' !== substr($mainNamespace, -1, 1))
                {
                    $mainNamespace .= '\\';
                }
                $len = strlen($mainNamespace);
                if($mainNamespace === substr($namespace, 0, $len))
                {
                    $namespaceSubPath = substr($namespace, $len);
                    $refClass = new \ReflectionClass($main);
                    $path = dirname($refClass->getFileName());
                    return File::path($path, str_replace('\\', DIRECTORY_SEPARATOR, $namespaceSubPath));
                }
            }
        }
        else
        {
            // 依靠 Composer PSR-4 配置的目录进行定位目录
            $prefixDirsPsr4 = $loader->getPrefixesPsr4();
            foreach($prefixDirsPsr4 as $keyNamespace => $paths)
            {
                $len = strlen($keyNamespace);
                if(substr($namespace, 0, $len) === $keyNamespace)
                {
                    if(isset($paths[1]))
                    {
                        return null;
                    }
                    return File::path($paths[0], str_replace('\\', DIRECTORY_SEPARATOR, substr($namespace, $len)));
                }
            }
        }
        return null;
    }

    /**
     * 获取类属性的值，值为beans配置或默认配置，支持传入Bean名称
     * 构造方法赋值无法取出
     *
     * @param string $className
     * @param string $propertyName
     * @return mixed
     */
    public static function getClassPropertyValue($className, $propertyName)
    {
        $value = BeanProxy::getInjectValue($className, $propertyName);
        if(null === $value)
        {
            if(!class_exists($className))
            {
                $className = BeanParser::getInstance()->getData()[$className]['className'];
            }
            $ref = new \ReflectionClass($className);
            $value = $ref->getDefaultProperties()[$propertyName] ?? null;
        }
        return $value;
    }

    /**
     * 获取Bean类缓存根目录
     *
     * @param string ...$paths
     * @return string
     */
    public static function getBeanClassCachePath(...$paths)
    {
        $main = Helper::getMain(App::getNamespace());
        $beanClassCache = $main->getConfig()['beanClassCache'] ?? null;
        if(null === $beanClassCache)
        {
            $beanClassCache = sys_get_temp_dir();
        }
        return File::path($beanClassCache, 'imiBeanCache', str_replace('\\', '-', App::getNamespace()), ...$paths);
    }

    /**
     * 获取IMI框架Bean类缓存目录
     *
     * @param string ...$paths
     * @return string
     */
    public static function getImiClassCachePath(...$paths)
    {
        return File::path(static::getBeanClassCachePath(), 'imi', ...$paths);
    }

    /**
     * 获取Worker进程Bean类缓存目录
     *
     * @param string ...$paths
     * @return string
     */
    public static function getWorkerClassCachePath(...$paths)
    {
        return static::getWorkerClassCachePathByWorkerID(Worker::getWorkerID(), ...$paths);
    }

    /**
     * 获取Worker进程Bean类缓存目录，手动传入workerID
     *
     * @param int $workerID
     * @param string ...$paths
     * @return string
     */
    public static function getWorkerClassCachePathByWorkerID($workerID, ...$paths)
    {
        return File::path(static::getBeanClassCachePath(), $workerID, ...$paths);
    }

    /**
     * 获取imi命令行
     *
     * @param string $toolName 工具名，如server
     * @param string $operation 操作名，如start
     * @return string
     */
    public static function getImiCmd($toolName, $operation)
    {
        $cmd = 'php ' . $_SERVER['argv'][0] . ' ' . $toolName . '/' . $operation;
        if(null !== ($appNamespace = Args::get('appNamespace')))
        {
            $cmd .= ' -appNamespace "' . $appNamespace . '"';
        }
        return $cmd;
    }
}