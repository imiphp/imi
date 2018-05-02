<?php
namespace Imi\Bean\Parser;

interface IParser
{
	/**
	 * 处理方法
	 * @param string $className
	 * @param \Imi\Bean\Annotation\Base $annotations
	 * @return void
	 */
	public function parse(string $className, \Imi\Bean\Annotation\Base $annotation);
}