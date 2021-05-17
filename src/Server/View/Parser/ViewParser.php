<?php

declare(strict_types=1);

namespace Imi\Server\View\Parser;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\IBean;
use Imi\Bean\Parser\BaseParser;
use Imi\Server\Route\RouteCallable;
use Imi\Server\View\Annotation\BaseViewOption;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Server\View\Annotation\View;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Util\Traits\TServerAnnotationParser;

/**
 * 视图注解处理器.
 */
class ViewParser extends BaseParser
{
    /**
     * 视图注解缓存.
     *
     * @var \Imi\Server\View\Annotation\View[]
     */
    private array $viewCache = [];

    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string                    $className  类名
     * @param string                    $target     注解目标类型（类/属性/方法）
     * @param string                    $targetName 注解目标名称
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
    }

    /**
     * 获取对应动作的视图注解.
     *
     * 返回：[View, BaseViewOption]
     *
     * @param callable $callable
     */
    public function getByCallable($callable): array
    {
        if ($callable instanceof RouteCallable)
        {
            $callable = $callable->getCallable();
        }
        if (!\is_array($callable))
        {
            return [new View(), null];
        }
        list($object, $methodName) = $callable;
        if ($object instanceof IBean)
        {
            $className = get_parent_class($object);
        }
        else
        {
            $className = \get_class($object);
        }
        $viewCache = &$this->viewCache;
        if (!isset($viewCache[$className][$methodName]))
        {
            $isClassView = false;
            /** @var View|null $view */
            $view = AnnotationManager::getMethodAnnotations($className, $methodName, View::class)[0] ?? null;
            if (null === $view)
            {
                /** @var View|null $view */
                $view = AnnotationManager::getClassAnnotations($className, View::class)[0] ?? null;
                if (null === $view)
                {
                    $view = new View();
                    $isClassView = true;
                }
                else
                {
                    $view = clone $view;
                }
            }
            else
            {
                $view = clone $view;
            }

            $viewOption = AnnotationManager::getMethodAnnotations($className, $methodName, BaseViewOption::class)[0] ?? null;
            if (null === $viewOption)
            {
                $viewOption = AnnotationManager::getClassAnnotations($className, BaseViewOption::class)[0] ?? null;
                if (null === $viewOption)
                {
                    $className = 'Imi\Server\View\Annotation\\' . Text::toPascalName($view->renderType) . 'View';
                    $viewOption = new $className();
                }
                else
                {
                    $viewOption = clone $viewOption;
                }
            }
            else
            {
                $viewOption = clone $viewOption;
            }
            if ($viewOption instanceof HtmlView)
            {
                // baseDir
                if (null === $viewOption->baseDir && !$isClassView)
                {
                    /** @var HtmlView|null $classViewOption */
                    $classViewOption = AnnotationManager::getClassAnnotations($className, HtmlView::class)[0] ?? null;
                    if ($classViewOption)
                    {
                        $viewOption->baseDir = $classViewOption->baseDir;
                    }
                }
                // template
                if (null === $viewOption->template)
                {
                    $viewOption->template = $isClassView ? File::path(Imi::getClassShortName($className), $methodName) : $methodName;
                }
            }

            $viewCache[$className][$methodName] = [$view, $viewOption];
        }

        return $viewCache[$className][$methodName];
    }
}
