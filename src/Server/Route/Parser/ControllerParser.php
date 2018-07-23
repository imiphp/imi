<?php
namespace Imi\Server\Route\Parser;

use Imi\Config;
use Imi\Util\Text;
use Imi\ServerManage;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Parser\BaseParser;
use Imi\Util\Traits\TServerAnnotationParser;

/**
 * 控制器注解处理器
 */
class ControllerParser extends BaseParser
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
		if($annotation instanceof \Imi\Server\Route\Annotation\Controller)
		{
			if(!isset($this->data[$className]))
			{
				$this->data[$className] = [
					'annotation'=>	$annotation,
					'methods'	=>	[],
				];
			}
		}
		else if($annotation instanceof \Imi\Server\Route\Annotation\Action)
		{
			if(!isset($this->data[$className][$targetName]))
			{
				$this->data[$className]['methods'][$targetName] = [];
			}
		}
		else if($annotation instanceof \Imi\Server\Route\Annotation\Route)
		{
			$this->data[$className]['methods'][$targetName]['routes'][] = $annotation;
		}
		else if($annotation instanceof \Imi\Server\Route\Annotation\Middleware)
		{
			switch($target)
			{
				case static::TARGET_CLASS:
					if(!isset($this->data[$className]['middlewares']))
					{
						$this->data[$className]['middlewares'] = [];
					}
					$this->data[$className]['middlewares'][] = $annotation;
					break;
				case static::TARGET_METHOD:
					if(!isset($this->data[$className]['methods'][$targetName]['middlewares']))
					{
						$this->data[$className]['methods'][$targetName]['middlewares'] = [];
					}
					$this->data[$className]['methods'][$targetName]['middlewares'][] = $annotation;
					break;
			}
		}
		else if($annotation instanceof \Imi\Server\Route\Annotation\WebSocket\WSConfig)
		{
			$this->data[$className]['methods'][$targetName]['WSConfig'] = $annotation;
		}
	}

}