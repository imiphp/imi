<?php
namespace Imi\Server\View\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Server\View\Annotation\View;
use Imi\Util\Traits\TServerAnnotationParser;
use Imi\Util\File;
use Imi\Util\Text;
use Imi\Util\Imi;
use Imi\Util\ClassObject;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\IBean;
use Imi\Server\Route\RouteCallable;

/**
 * 视图注解处理器
 */
class ViewParser extends BaseParser
{
    use TServerAnnotationParser;
    
    /**
     * 视图注解缓存
     *
     * @var \Imi\Server\View\Annotation\View[]
     */
    private $viewCache = [];

    /**
     * 处理方法
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string $className 类名
     * @param string $target 注解目标类型（类/属性/方法）
     * @param string $targetName 注解目标名称
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
    }

    /**
     * 获取对应动作的视图注解
     * 
     * @param callable $callable
     * @return \Imi\Server\View\Annotation\View
     */
    public function getByCallable($callable)
    {
        if($callable instanceof RouteCallable)
        {
            $callable = $callable->getCallable();
        }
        if(!is_array($callable))
        {
            $view = new View();
            return $view;
        }
        list($object, $methodName) = $callable;
        if($object instanceof IBean)
        {
            $className = get_parent_class($object);
        }
        else
        {
            $className = get_class($object);
        }
        $viewCache = &$this->viewCache;
        if(!isset($viewCache[$className][$methodName]))
        {
            $shortClassName = Imi::getClassShortName($className);
            $isClassView = false;
            $view = AnnotationManager::getMethodAnnotations($className, $methodName, View::class)[0] ?? null;
            if(null === $view)
            {
                $view = AnnotationManager::getClassAnnotations($className, View::class)[0] ?? null;
                if(null === $view)
                {
                    $view = new View([
                        'template' => File::path($shortClassName, $methodName),
                    ]);
                }
                else
                {
                    $view = clone $view;
                    $isClassView = true;
                }
            }
            else
            {
                $view = clone $view;
            }

            // baseDir
            if(null === $view->baseDir && !$isClassView)
            {
                $classView = AnnotationManager::getClassAnnotations($className, View::class)[0] ?? null;
                if($classView)
                {
                    $view->baseDir = $classView->baseDir;
                }
            }
            // template
            if(null === $view->template)
            {
                $view->template = $methodName;
            }
            $viewCache[$className][$methodName] = $view;
        }
        return $viewCache[$className][$methodName];
    }
}