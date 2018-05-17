<?php
namespace Imi\Main;

use Imi\Config;

/**
 * 项目主类基类
 */
abstract class AppBaseMain extends BaseMain
{
	/**
	 * 加载配置
	 * @return void
	 */
	protected function loadConfig()
	{
		parent::loadConfig();
		foreach(Config::get('@' . $this->serverName . '.configs') as $name => $fileName)
		{
			Config::addConfig($name, include $fileName);
		}
	}
}