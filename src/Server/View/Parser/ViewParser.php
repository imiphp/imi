<?php
namespace Imi\Server\View\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Server\View\Annotation\View;
use Imi\Util\Traits\TServerAnnotationParser;
use Imi\Util\File;
use Imi\Util\Text;
use Imi\Util\Imi;
use Imi\Util\ClassObject;

/**
 * 视图注解处理器
 */
class ViewParser extends BaseParser
{
	use TServerAnnotationParser;
	
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
		if($annotation instanceof View)
		{
			switch($target)
			{
				case static::TARGET_CLASS:
					$this->data[$className] = [
						'view'		=>	$annotation,
						'methods'	=>	[],
					];
					break;
				case static::TARGET_METHOD:
					$this->data[$className]['methods'][$targetName]['view'] = $annotation;
					break;
			}
		}
	}

	public function getByCallable($callable)
	{
		if(!is_array($callable))
		{
			return null;
		}
		list($object, $methodName) = $callable;
		if(ClassObject::isAnymous($object))
		{
			$className = get_parent_class($object);
		}
		else
		{
			$className = get_class($object);
		}
		$shortClassName = Imi::getClassShortName($className);
		if(isset($this->data[$className]['methods'][$methodName]['view']))
		{
			$view = clone $this->data[$className]['methods'][$methodName]['view'];
		}
		else if(isset($this->data[$className]['view']))
		{
			$view = clone $this->data[$className]['view'];
			$view->template = File::path(Text::isEmpty($view->template) ? $shortClassName : $view->template, $methodName);
		}
		else
		{
			$view = new View([
				'template'	=>	File::path($shortClassName, $methodName),
			]);
		}
		return $view;
	}
}