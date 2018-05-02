<?php
namespace Imi\Main;

use Imi\Util\TSingleton;

/**
 * 主类基类
 */
abstract class BaseMain implements IMain
{
	/**
	 * 当前模块根路径
	 * @var string
	 */
	private $path;

	/**
	 * 当前模块命名空间
	 * @var string
	 */
	private $namespace;

	/**
	 * 配置
	 * @var array
	 */
	private $config = [];

	public function init()
	{
		$this->loadConfig();
		$this->__init();
	}

	/**
	 * 加载配置
	 * @return void
	 */
	private function loadConfig()
	{
		$fileName = $this->getPath() . DIRECTORY_SEPARATOR . 'config/config.php';
		if(is_file($fileName))
		{
			$this->config = include $fileName;
		}
	}

	/**
	 * 获取配置
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
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
		return isset($this->config['beanScan']) ? $this->config['beanScan'] : [];
	}
}