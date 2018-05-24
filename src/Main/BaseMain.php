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
	 * 模块名称
	 * @var string
	 */
	protected $moduleName;

	public function __construct(string $moduleName)
	{
		$this->moduleName = $moduleName;
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
			Config::addConfig('@' . $this->moduleName, include $fileName);
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
		return Config::get('@' . $this->moduleName . '.beanScan', []);
	}

	/**
	 * 获取要初始化的原子计数名称
	 * @return array
	 */
	public function getAtomics(): array
	{
		return Config::get('@' . $this->moduleName . '.atomics', []);
	}

	/**
	 * 获取要初始化的通道名称
	 * @return array
	 */
	public function getChannels(): array
	{
		return Config::get('@' . $this->moduleName . '.channels', []);
	}

	/**
	 * 获取要初始化的协程通道名称
	 * @return array
	 */
	public function getCoroutineChannels(): array
	{
		return Config::get('@' . $this->moduleName . '.coroutineChannels', []);
	}

	/**
	 * 获取模块名称
	 * @return string
	 */
	public function getModuleName(): string
	{
		return $this->moduleName;
	}
}