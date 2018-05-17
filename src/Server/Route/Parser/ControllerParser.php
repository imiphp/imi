<?php
namespace Imi\Server\Route\Parser;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Parser\BaseParser;
use Imi\ServerManage;
use Imi\Util\Text;
use Imi\Config;

/**
 * 控制器注解处理器
 */
class ControllerParser extends BaseParser
{
	/**
	 * 根据服务器获取的控制器缓存
	 * @var array
	 */
	private $cache = [];

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
	}

	/**
	 * 根据服务器获取对应的控制器数据
	 * @param string $serverName
	 * @return array
	 */
	public function getByServer($serverName)
	{
		if(isset($this->cache[$serverName]))
		{
			return $this->cache[$serverName];
		}
		$namespace = ServerManage::getServer($serverName)->getConfig()['namespace'];
		$result = [];
		foreach($this->data as $className => $item)
		{
			if(Text::startwith($className, $namespace))
			{
				$result[$className] = $item;
			}
		}
		$this->cache[$serverName] = $result;
		return $result;
	}
}