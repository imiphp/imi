<?php
namespace Imi\Tool;

use Imi\App;
use Imi\Config;
use Imi\Util\Imi;
use Imi\Util\Args;
use Imi\Util\File;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Parser\ToolParser;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Tool\Annotation\Operation;

abstract class Tool
{
    private static $toolName, $toolOperation;

    public static function initTool()
    {
        if(!isset($_SERVER['argv'][1]))
        {
            throw new \RuntimeException(sprintf('Tool args error!'));
        }
        if(false === strpos($_SERVER['argv'][1], '/'))
        {
            throw new \RuntimeException(sprintf('Tool name and operation not found!'));
        }
        // 工具名/操作名
        list(static::$toolName, static::$toolOperation) = explode('/', $_SERVER['argv'][1]);
        static::init();
        Imi::setProcessName('tool');
    }

    public static function run()
    {
        // 获取回调
        $callable = ToolParser::getInstance()->getCallable(static::$toolName, static::$toolOperation);
        if(null === $callable)
        {
            throw new \RuntimeException(sprintf('Tool %s does not exists!', $_SERVER['argv'][1]));
        }
        if(Args::get('h'))
        {
            // 帮助
            $className = get_parent_class($callable[0]);
            $refClass = new \ReflectionClass($className);
            $required = [];
            $other = [];
            foreach(ToolParser::getInstance()->getData()['class'][$className]['Methods'][$callable[1]]['Args'] ?? [] as $arg)
            {
                if($arg->required)
                {
                    $required[] = $arg;
                }
                else
                {
                    $other[] = $arg;
                }
            }
            echo '工具名称：', static::$toolName, '/', static::$toolOperation, PHP_EOL, PHP_EOL;
            echo static::parseComment($refClass->getMethod($callable[1])->getDocComment()), PHP_EOL;
            if(isset($required[0]))
            {
                echo PHP_EOL, '必选参数：', PHP_EOL;
                foreach($required as $arg)
                {
                    echo '-', $arg->name, ' ', $arg->comments, PHP_EOL;
                }
            }
            if(isset($other[0]))
            {
                echo PHP_EOL, '可选参数：', PHP_EOL;
                foreach($other as $arg)
                {
                    echo '-', $arg->name, ' ', $arg->comments, PHP_EOL;
                }
            }
        }
        else
        {
            // 执行参数
            $args = static::getCallToolArgs($callable, static::$toolName, static::$toolOperation);
            $result = ToolParser::getInstance()->getToolClassAndMethod(static::$toolName, static::$toolOperation);
            if(!$result)
            {
                throw new \RuntimeException(sprintf('Tool %s/%s does not exists!', static::$toolName, static::$toolOperation));
            }
            $operationAnnotation = AnnotationManager::getMethodAnnotations($result['class'], $result['method'], Operation::class)[0];
            // 执行工具操作
            if($operationAnnotation->co)
            {
                imigo(function() use($callable, $args) {
                    call_user_func_array($callable, $args);
                });
            }
            else
            {
                call_user_func_array($callable, $args);
            }
            swoole_event_wait();
        }
        
    }

    /**
     * 获取当前命令行工具名称
     *
     * @return string
     */
    public static function getToolName()
    {
        return static::$toolName;
    }

    /**
     * 获取当前命令行工具操作名称
     *
     * @return string
     */
    public static function getToolOperation()
    {
        return static::$toolOperation;
    }

    /**
     * 初始化
     * @return void
     */
    private static function init()
    {
        // 跳过初始化的工具
        foreach(Config::get('@Imi.skipInitTools') as $tool)
        {
            if(static::$toolName === $tool[0] && static::$toolOperation === $tool[1])
            {
                return;
            }
        }

        // 仅初始化项目及组件
        $initMains = [Helper::getMain(App::getNamespace())];
        foreach(Helper::getAppMains() as $mainName => $main)
        {
            foreach($main->getConfig()['components'] ?? [] as $componentName => $namespace)
            {
                $componentMain = Helper::getMain($namespace);
                if(null !== $componentMain)
                {
                    $initMains[] = $componentMain;
                }
            }
        }
        Annotation::getInstance()->init($initMains);

        RequestContext::create();
        // 获取配置
        $pools = $caches = [];
        foreach(Helper::getMains() as $main)
        {
            $pools = array_merge($pools, $main->getConfig()['pools'] ?? []);
            $caches = array_merge($caches, $main->getConfig()['caches'] ?? []);
        }
        // 同步池子初始化
        foreach($pools as $name => $pool)
        {
            if(isset($pool['sync']))
            {
                $pool = $pool['sync'];
                PoolManager::addName($name, $pool['pool']['class'], new PoolConfig($pool['pool']['config']), $pool['resource']);
            }
        }
        // 缓存初始化
        foreach($caches as $name => $cache)
        {
            CacheManager::addName($name, $cache['handlerClass'], $cache['option']);
        }
    }

    /**
     * 获取执行参数
     * @param callable $callable
     * @param string $tool
     * @param string $operation
     * @return array
     */
    private static function getCallToolArgs($callable, $tool, $operation)
    {
        $className = get_parent_class($callable[0]);
        $methodRef = new \ReflectionMethod($className, $callable[1]);
        $annotations = ToolParser::getInstance()->getData()['class'][$className]['Methods'][$methodRef->name]['Args'] ?? [];
        $args = [];
        foreach($methodRef->getParameters() as $param)
        {
            $annotation = $annotations[$param->name] ?? null;
            if(null === $annotation)
            {
                $value = $param->isOptional() ? $param->getDefaultValue() : null;
            }
            else if(Args::exists($annotation->name))
            {
                $value = static::parseArgValue(Args::get($annotation->name), $annotation);
            }
            else if($annotation->required)
            {
                throw new \InvalidArgumentException(sprintf('tool %s/%s param %s is required', $tool, $operation, $annotation->name));
            }
            else
            {
                $value = $annotation->default;
            }
            $args[] = $value;
        }
        return $args;
    }

    /**
     * 处理参数值
     * @param string $value
     * @param Arg $annotation
     * @return mixed
     */
    private static function parseArgValue($value, Arg $annotation)
    {
        switch($annotation->type)
        {
            case ArgType::STRING:
                break;
            case ArgType::INT:
                $value = (int)$value;
                break;
            case ArgType::FLOAT:
            case ArgType::DOUBLE:
                $value = (float)$value;
                break;
            case ArgType::BOOL:
            case ArgType::BOOLEAN:
                $value = (bool)json_decode($value);
                break;
            case ArgType::ARRAY:
                $value = explode(',', $value);
                break;
        }
        return $value;
    }

    /**
     * 处理注释
     * @param string $content
     * @return string
     */
    private static function parseComment($content)
    {
        return trim(preg_replace('/@.+\n/', '', preg_replace('/\/*\s*\*\s*\/*/', PHP_EOL, $content)));
    }

}