<?php
namespace Imi\Db\Parser;

use Imi\Bean\Parser\BaseParser;

class DbParser extends BaseParser
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
		if($annotation instanceof \Imi\Db\Annotation\Transaction)
		{
			$this->data[$className][$targetName] = [
				'Transaction'	=>	$annotation,
			];
		}
	}

	/**
	 * 获取方法事务注解
	 *
	 * @param string $className
	 * @param string $methodName
	 * @return \Imi\Db\Annotation\Transaction|null
	 */
	public function getMethodTransaction($className, $methodName)
	{
		return $this->data[$className][$methodName]['Transaction'] ?? null;
	}
}