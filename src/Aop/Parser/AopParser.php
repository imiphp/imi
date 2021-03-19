<?php

namespace Imi\Aop\Parser;

use Imi\Aop\Annotation\Inject;
use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Parser\BaseParser;
use Imi\Bean\ReflectionContainer;
use Imi\Server\Annotation\ServerInject;

class AopParser extends BaseParser
{
    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string                    $className  类名
     * @param string                    $target     注解目标类型（类/属性/方法）
     * @param string                    $targetName 注解目标名称
     *
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        $annotationClass = \get_class($annotation);
        switch ($annotationClass)
        {
            case Inject::class:
            case RequestInject::class:
            case ServerInject::class:
                /** @var Inject|RequestInject $annotation */
                if ('' === $annotation->name && self::TARGET_PROPERTY === $target)
                {
                    $propRef = ReflectionContainer::getPropertyReflection($className, $targetName);
                    if (method_exists($propRef, 'hasType') && $propRef->hasType())
                    {
                        $type = $propRef->getType();
                        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin())
                        {
                            $annotation->name = $type->getName();

                            return;
                        }
                    }
                    $comment = $propRef->getDocComment();
                    $factory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
                    $docblock = $factory->create($comment);
                    $tag = $docblock->getTagsByName('var')[0] ?? null;
                    if ($tag)
                    {
                        $annotation->name = ltrim($tag->__toString(), '\\');
                    }
                    else
                    {
                        throw new \RuntimeException(sprintf('@%s in %s::$%s must set name', $annotationClass, $className, $target));
                    }
                }
                break;
        }
    }
}
