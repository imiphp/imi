<?php
namespace Imi\Util\Traits;

use Imi\Util\Text;
use Imi\ServerManage;

/**
 * 注解处理器按服务器名获取
 */
trait TServerAnnotationParser
{
	/**
	 * 根据服务器获取的控制器缓存
	 * @var array
	 */
	private $cache = [];
	
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