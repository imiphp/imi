<?php

declare(strict_types=1);

namespace Imi\Server\View\Parser;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\IBean;
use Imi\Bean\Parser\BaseParser;
use Imi\Server\View\Annotation\BaseViewOption;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Server\View\Annotation\View;
use Imi\Util\DelayServerBeanCallable;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Text;

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
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
    }

    /**
     * 获取对应动作的视图注解.
     *
     * 返回：[View, BaseViewOption]
     */
    public function getByCallable(callable $callable): array
    {
        if (\is_array($callable))
        {
            [$object, $methodName] = $callable;
            if ($object instanceof IBean)
            {
                $className = get_parent_class($object);
            }
            else
            {
                $className = \get_class($object);
            }
        }
        elseif ($callable instanceof DelayServerBeanCallable)
        {
            $className = $callable->getBeanName();
            $methodName = $callable->getMethodName();
        }
        else
        {
            return [new View(), null];
        }
        $viewCache = &$this->viewCache;
        if (!isset($viewCache[$className][$methodName]))
        {
            $isClassView = false;
            $annotations = AnnotationManager::getMethodAnnotations($className, $methodName, [
                View::class,
                BaseViewOption::class,
            ]);
            /** @var View|null $view */
            $view = $annotations[View::class][0] ?? null;
            if (null === $view)
            {
                /** @var View|null $view */
                $view = AnnotationManager::getClassAnnotations($className, View::class, true, true);
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

            $viewOption = $annotations[BaseViewOption::class][0] ?? null;
            if (null === $viewOption)
            {
                $viewOption = AnnotationManager::getClassAnnotations($className, BaseViewOption::class, true, true);
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
                    $classViewOption = AnnotationManager::getClassAnnotations($className, HtmlView::class, true, true);
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
