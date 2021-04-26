<?php

namespace Imi\Tool\Tools\Generate\Facade;

use Imi\Bean\Annotation;
use Imi\Bean\Parser\BeanParser;
use Imi\Bean\ReflectionUtil;
use Imi\Facade\Annotation\Facade;
use Imi\Main\Helper;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\ArgType;
use Imi\Util\File;
use Imi\Util\Imi;
use ReflectionClass;
use ReflectionMethod;

/**
 * @Tool("generate")
 */
class FacadeGenerate
{
    /**
     * 生成门面类.
     *
     * @Operation("facade")
     *
     * @Arg(name="facadeClass", type=ArgType::STRING, required=true, comments="生成的门面类")
     * @Arg(name="class", type=ArgType::STRING, required=true, comments="要绑定的类")
     * @Arg(name="request", type=ArgType::BOOL, default=false, comments="是否请求上下文门面")
     *
     * @param string $facadeClass
     * @param string $class
     * @param bool   $request
     *
     * @return void
     */
    public function generate($facadeClass, $class, $request)
    {
        Annotation::getInstance()->init(Helper::getAppMains());
        if (class_exists($class))
        {
            $fromClass = $class;
        }
        else
        {
            $data = BeanParser::getInstance()->getData();
            if (isset($data[$class]))
            {
                $fromClass = $data[$class]['className'];
            }
            else
            {
                throw new \RuntimeException(sprintf('Class %s does not found', $class));
            }
        }
        $namespace = Imi::getClassNamespace($facadeClass);
        $shortClassName = Imi::getClassShortName($facadeClass);
        $fileName = Imi::getNamespacePath($namespace);
        if (null === $fileName)
        {
            throw new \RuntimeException(sprintf('Get namespace %s path failed', $namespace));
        }
        $fileName = File::path($fileName, $shortClassName . '.php');
        $facadeAnnotation = Annotation::toComments(new Facade([
            'class'     => $class,
            'request'   => $request,
        ]));
        $refClass = new ReflectionClass($fromClass);
        $methods = [];
        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
        {
            $methodName = $method->getName();
            // 构造、析构方法去除
            if (\in_array($methodName, ['__construct', '__destruct']))
            {
                continue;
            }
            if (preg_match('/@return\s+([^\s]+)/', $method->getDocComment(), $matches) > 0)
            {
                $returnType = $matches[1];
            }
            elseif ($method->hasReturnType())
            {
                $returnType = ReflectionUtil::getTypeComments($method->getReturnType(), $method->getDeclaringClass()->getName());
            }
            else
            {
                $returnType = 'mixed';
            }
            $params = [];
            foreach ($method->getParameters() as $param)
            {
                $result = '';
                // 类型
                $paramType = $param->getType();
                if ($paramType)
                {
                    $paramType = ReflectionUtil::getTypeCode($paramType, $param->getDeclaringClass()->getName());
                }
                $result .= null === $paramType ? '' : ((string) $paramType . ' ');
                if ($param->isPassedByReference())
                {
                    // 引用传参
                    $result .= '&';
                }
                elseif ($param->isVariadic())
                {
                    // 可变参数...
                    $result .= '...';
                }
                // $参数名
                $result .= '$' . $param->name;
                // 默认值
                if ($param->isDefaultValueAvailable())
                {
                    $result .= ' = ' . var_export($param->getDefaultValue(), true);
                }
                $params[] = $result;
            }
            $params = implode(', ', $params);
            $methods[] = '@method static ' . $returnType . ' ' . $methodName . '(' . $params . ')';
        }
        // @phpstan-ignore-next-line
        $content = (function () use ($namespace, $facadeAnnotation, $methods, $shortClassName) {
            ob_start();
            include __DIR__ . '/template.tpl';

            return ob_get_clean();
        })();
        File::putContents($fileName, $content);
    }
}
