<?php
namespace Imi\Tool\Tools\Generate\Facade;

use Imi\App;
use Imi\Util\Imi;
use Imi\Util\File;
use Imi\Main\Helper;
use ReflectionClass;
use Imi\Tool\ArgType;
use ReflectionMethod;
use Imi\Bean\Annotation;
use Imi\Bean\BeanFactory;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Bean\Parser\BeanParser;
use Imi\Facade\Annotation\Facade;
use Imi\Tool\Annotation\Operation;

/**
 * @Tool("generate")
 */
class FacadeGenerate
{
    /**
     * 生成门面类
     * @Operation("facade")
     *
     * @Arg(name="facadeClass", type=ArgType::STRING, required=true, comments="生成的门面类")
     * @Arg(name="class", type=ArgType::STRING, required=true, comments="要绑定的类")
     * @Arg(name="request", type=ArgType::BOOL, default=false, comments="是否请求上下文门面")
     * @return void
     */
    public function generate($facadeClass, $class, $request)
    {
        Annotation::getInstance()->init(Helper::getAppMains());
        if(class_exists($class))
        {
            $fromClass = $class;
        }
        else
        {
            $data = BeanParser::getInstance()->getData();
            if(isset($data[$class]))
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
        if(null === $fileName)
        {
            throw new \RuntimeException(sprintf('Get namespace %s path failed', $namespace));
        }
        $fileName = File::path($fileName, $shortClassName . '.php');
        $facadeAnnotation = Annotation::toComments(new Facade([
            'class'     =>  $class,
            'request'   =>  $request,
        ]));
        $refClass = new ReflectionClass($fromClass);
        $methods = [];
        foreach($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
        {
            if(preg_match('/@return\s+([^\s]+)/', $method->getDocComment(), $matches) > 0)
            {
                $returnType = $matches[1];
            }
            else if($method->hasReturnType())
            {
                $returnType = $method->getReturnType();
                if($returnType->allowsNull())
                {
                    $returnType = $returnType->getName() . '|null';
                }
                else
                {
                    $returnType = $returnType->getName();
                }
            }
            else
            {
                $returnType = 'mixed';
            }
            $params = [];
            foreach($method->getParameters() as $param)
            {
                if($param->hasType())
                {
                    $type = $param->getType();
                    if($type->allowsNull())
                    {
                        $type = $type->getName() . '|null';
                    }
                    else
                    {
                        $type = $type->getName();
                    }
                }
                else
                {
                    $type = 'mixed';
                }
                if($param->isDefaultValueAvailable())
                {
                    $defaultValue = ' = ' . var_export($param->getDefaultValue(), true);
                }
                else
                {
                    $defaultValue = '';
                }
                $params[] = $type . ' $' . $param->getName() . $defaultValue;
            }
            $params = implode(', ', $params);
            $methods[] = '@method static ' . $returnType . ' ' . $method->getName() . '(' . $params . ')';
        }
        $content = (function() use($namespace, $facadeAnnotation, $methods, $shortClassName){
            ob_start();
            include __DIR__ . '/template.tpl';
            return ob_get_clean();
        })();
        file_put_contents($fileName, $content);
    }

}
