<?php
namespace Imi\Bean\Parser;

class ClassEventParser extends BaseParser
{
	/**
	 * 处理方法
	 * @param string $className
	 * @param \Imi\Bean\Annotation\Base $annotation
	 * @return void
	 */
	public function parse(string $className, \Imi\Bean\Annotation\Base $annotation)
	{
		if($annotation instanceof \Imi\Bean\Annotation\ClassEventListener)
		{
			if(!isset($this->data[$annotation->className][$annotation->eventName]))
			{
				$this->data[$annotation->className][$annotation->eventName] = [];
			}
			$this->data[$annotation->className][$annotation->eventName][] = [
				'className'		=>	$className,
				'priority'		=>	$annotation->priority,
			];
		}
	}
}