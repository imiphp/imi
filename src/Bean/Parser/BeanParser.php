<?php
namespace Imi\Bean\Parser;

class BeanParser extends BaseParser
{
	/**
	 * 处理方法
	 * @param string $className
	 * @param \Imi\Bean\Annotation\Base $annotation
	 * @return void
	 */
	public function parse(string $className, \Imi\Bean\Annotation\Base $annotation)
	{
		if($annotation instanceof \Imi\Bean\Annotation\Bean)
		{
			$beanName = $annotation->name ?? $className;
			$this->data[$beanName] = [
				'className'		=>	$className,
				'instanceType'	=>	$annotation->instanceType,
			];
		}
	}
}