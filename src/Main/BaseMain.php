<?php
namespace Imi\Main;

use Imi\Config;

/**
 * 主类基类
 */
abstract class BaseMain implements IMain
{
	/**
	 * 当前模块根路径
	 * @var string
	 */
	protected $path;

	/**
	 * 当前模块命名空间
	 * @var string
	 */
	protected $namespace;

	/**
	 * 服务器名称
	 * @var string
	 */
	protected $serverName;

	public function __construct(string $serverName)
	{
		$this->serverName = $serverName;
	}

	public function init()
	{
		$this->loadConfig();
		$this->__init();
	}

	/**
	 * 加载配置
	 * @return void
	 */
	protected function loadConfig()
	{
		$fileName = $this->getPath() . DIRECTORY_SEPARATOR . 'config/config.php';
		if(is_file($fileName))
		{
			Config::addConfig('@' . $this->serverName, include $fileName);
		}
	}

	/**
	 * 获取当前模块根路径
	 * @return string
	 */
	public function getPath(): string
	{
		if(null === $this->path)
		{
			$ref = new \ReflectionClass($this);
			$this->path = dirname($ref->getFileName());
		}
		return $this->path;
	}

	/**
	 * 获取当前模块命名空间
	 * @return string
	 */
	public function getNamespace(): string
	{
		if(null === $this->namespace)
		{
			$this->namespace = str_replace(DIRECTORY_SEPARATOR, '\\', dirname(str_replace('\\', DIRECTORY_SEPARATOR, get_called_class())));
		}
		return $this->namespace;
	}

	/**
	 * 获取要扫描的bean的命名空间
	 * @return array
	 */
	public function getBeanScan(): array
	{
		return Config::get('@' . $this->serverName . '.beanScan', []);
	}

	/**
	 * 获取服务器名称
	 * @return string
	 */
	public function getServerName(): string
	{
		return $this->serverName;
	}
}