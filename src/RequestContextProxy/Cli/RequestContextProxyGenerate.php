<?php

declare(strict_types=1);

namespace Imi\RequestContextProxy\Cli;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Bean\BeanManager;
use Imi\Bean\ReflectionUtil;
use Imi\Bean\Util\AttributeUtil;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\Util\File;
use Imi\Util\Imi;

#[Command(name: 'generate')]
class RequestContextProxyGenerate
{
    /**
     * 生成请求上下文代理类.
     */
    #[CommandAction(name: 'requestContextProxy')]
    #[Option(name: 'target', type: \Imi\Cli\ArgType::STRING, required: true, comments: '生成的目标类')]
    #[Option(name: 'class', type: \Imi\Cli\ArgType::STRING, required: true, comments: '要绑定的代理类名')]
    #[Option(name: 'name', type: \Imi\Cli\ArgType::STRING, required: true, comments: '请求上下文中的名称')]
    #[Option(name: 'bean', type: \Imi\Cli\ArgType::STRING, comments: '生成的目标类的 Bean 名称')]
    #[Option(name: 'interface', type: \Imi\Cli\ArgType::STRING, comments: '生成的目标类要实现的接口')]
    #[Option(name: 'recursion', type: \Imi\Cli\ArgType::BOOLEAN, default: true, comments: '是否启用 Bean 递归特性')]
    public function generate(string $target, string $class, string $name, ?string $bean, ?string $interface, bool $recursion): void
    {
        if (class_exists($class) || interface_exists($class))
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
        $fileName = Imi::getNamespacePath($namespace, true);
        if (null === $fileName)
        {
            throw new \RuntimeException(sprintf('Get namespace %s path failed', $namespace));
        }
        $fileName = File::path($fileName, $shortClassName . '.php');
        $attributes = [];
        $attributes[] = new RequestContextProxy([
            'class' => $class,
            'name'  => $name,
        ]);
        if (null !== $bean)
        {
            $attributes[] = new Bean([
                'name'      => $bean,
                'recursion' => $recursion,
            ]);
        }
        $classAttributesCode = AttributeUtil::generateAttributesCode($attributes);
        $refClass = new \ReflectionClass($fromClass);
        $methods = [];
        foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            if ($method->isStatic())
            {
                continue;
            }
            $methodName = $method->getName();
            // 构造、析构方法去除
            if (\in_array($methodName, ['__construct', '__destruct']))
            {
                continue;
            }
            $docComment = $method->getDocComment();
            if (false !== $docComment && preg_match('/@return\s+([^\s]+)/', $docComment, $matches) > 0)
            {
                $class = $matches[1];
                if ('self' === $class || 'static' === $class)
                {
                    $returnType = '\\' . $method->getDeclaringClass()->getName();
                }
                elseif ('\\' === $class[0])
                {
                    $returnType = $class;
                }
                else
                {
                    $fullClass = $method->getDeclaringClass()->getNamespaceName() . '\\' . $class;
                    if (class_exists($fullClass) || interface_exists($fullClass, false) || trait_exists($fullClass, false))
                    {
                        $returnType = '\\' . $fullClass;
                    }
                    elseif (class_exists($class))
                    {
                        $returnType = '\\' . $class;
                    }
                    else
                    {
                        $returnType = $class;
                    }
                }
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
                    $defaultValue = $param->getDefaultValue();
                    $result .= ' = ' . (\is_array($defaultValue) ? '[]' : var_export($defaultValue, true));
                }
                $params[] = $result;
            }
            $params = implode(', ', $params);
            $item = $returnType . ' ' . $methodName . '(' . $params . ')';
            $methods[] = '@method ' . $item;
            $methods[] = '@method static ' . $item;
        }
        $methodCodes = '';
        if (null !== $interface)
        {
            $refInterface = new \ReflectionClass($interface);
            foreach ($refInterface->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
            {
                $methodName = $method->name;
                if ('__construct' === $methodName)
                {
                    continue;
                }
                $paramsTpls = BeanFactory::getMethodParamTpls($method);
                $methodReturnType = BeanFactory::getMethodReturnType($method);
                $returnsReference = $method->returnsReference() ? '&' : '';
                if ('void' !== ReflectionUtil::getTypeCode($method->getReturnType(), $method->getDeclaringClass()->getName()))
                {
                    $code = 'return ';
                }
                else
                {
                    $code = '';
                }
                if ($method->isStatic())
                {
                    $code = "throw new \RuntimeException('Unsupport method');";
                    $static = 'static ';
                }
                else
                {
                    $code .= "self::__getProxyInstance()->{$methodName}({$paramsTpls['call']});";
                    $static = '';
                }
                $methodCodes .= <<<TPL
                    /**
                     * {@inheritDoc}
                     */
                    public {$static}function {$returnsReference}{$methodName}({$paramsTpls['define']}){$methodReturnType}
                    {
                        {$code}
                    }


                TPL;
            }
        }
        // @phpstan-ignore-next-line
        $content = (static function () use ($namespace, $classAttributesCode, $methods, $shortClassName, $interface, $methodCodes): string {
            ob_start();
            include __DIR__ . '/template.tpl';

            return ob_get_clean();
        })();
        File::putContents($fileName, $content);
    }
}
