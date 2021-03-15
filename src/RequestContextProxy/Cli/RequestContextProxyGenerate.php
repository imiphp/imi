<?php

declare(strict_types=1);

namespace Imi\RequestContextProxy\Cli;

use Imi\Bean\Annotation;
use Imi\Bean\BeanManager;
use Imi\Main\Helper;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
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
class RequestContextProxyGenerate
{
    /**
     * 生成请求上下文代理类.
     *
     * @Operation("requestContextProxy")
     *
     * @Arg(name="target", type=ArgType::STRING, required=true, comments="生成的目标类")
     * @Arg(name="class", type=ArgType::STRING, required=true, comments="要绑定的代理类名")
     * @Arg(name="name", type=ArgType::STRING, required=true, comments="请求上下文中的名称")
     *
     * @return void
     */
    public function generate(string $target, string $class, string $name): void
    {
        Annotation::getInstance()->init(Helper::getAppMains());
        if (class_exists($class))
        {
            $fromClass = $class;
        }
        else
        {
            $data = BeanManager::get($class);
            if ($data)
            {
                $fromClass = $data['className'];
            }
            else
            {
                throw new \RuntimeException(sprintf('Class %s does not found', $class));
            }
        }
        $namespace = Imi::getClassNamespace($target);
        $shortClassName = Imi::getClassShortName($target);
        $fileName = Imi::getNamespacePath($namespace);
        if (null === $fileName)
        {
            throw new \RuntimeException(sprintf('Get namespace %s path failed', $namespace));
        }
        $fileName = File::path($fileName, $shortClassName . '.php');
        $requestContextProxyAnnotation = Annotation::toComments(new RequestContextProxy([
            'class' => $class,
            'name'  => $name,
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
                $returnType = $method->getReturnType();
                if ($returnType->allowsNull())
                {
                    // @phpstan-ignore-next-line
                    $returnType = $returnType->getName() . '|null';
                }
                else
                {
                    // @phpstan-ignore-next-line
                    $returnType = $returnType->getName();
                }
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
                    // @phpstan-ignore-next-line
                    $paramType = $paramType->getName();
                }
                if (null !== $paramType && $param->allowsNull())
                {
                    $paramType = '?' . $paramType;
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
            $item = $returnType . ' ' . $methodName . '(' . $params . ')';
            $methods[] = '@method ' . $item;
            if (!$method->isStatic())
            {
                $methods[] = '@method static ' . $item;
            }
        }
        // @phpstan-ignore-next-line
        $content = (function () use ($namespace, $requestContextProxyAnnotation, $methods, $shortClassName): string {
            ob_start();
            include __DIR__ . '/template.tpl';

            return ob_get_clean();
        })();
        file_put_contents($fileName, $content);
    }
}
