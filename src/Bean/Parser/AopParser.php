<?php
namespace Imi\Bean\Parser;

class AopParser extends BaseParser
{
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
		if($annotation instanceof \Imi\Bean\Annotation\Aspect)
		{
			$this->data[$className] = [
				'className'	=>	$className,
				'aspect'	=>	$annotation,
			];
		}
		else if($annotation instanceof \Imi\Bean\Annotation\PointCut)
		{
			$this->data[$className][$target][$targetName]['pointCut'] = $annotation;
		}
		else if($annotation instanceof \Imi\Bean\Annotation\Before)
		{
			$this->data[$className][$target][$targetName]['before'] = true;
		}
		else if($annotation instanceof \Imi\Bean\Annotation\After)
		{
			$this->data[$className][$target][$targetName]['after'] = true;
		}
		else if($annotation instanceof \Imi\Bean\Annotation\AfterReturning)
		{
			$this->data[$className][$target][$targetName]['afterReturning'] = true;
		}
		else if($annotation instanceof \Imi\Bean\Annotation\AfterThrowing)
		{
			$this->data[$className][$target][$targetName]['afterThrowing'] = $annotation;
		}
		else if($annotation instanceof \Imi\Bean\Annotation\Around)
		{
			$this->data[$className][$target][$targetName]['around'] = true;
		}
		else if($annotation instanceof \Imi\Bean\Annotation\Inject)
		{
			$this->data[$className][$target][$targetName]['inject'] = $annotation;
		}
	}
}