<?php
namespace Imi\Bean;

use Imi\App;
use Imi\Main\Helper as MainHelper;

/**
 * 注解处理类
 */
class Annotation
{
	/**
	 * 加载器
	 * @var AnnotationLoader
	 */
	private $loader;

	/**
	 * 处理器
	 * @var AnnotationParser
	 */
	private $paser;

	public function __construct()
	{
		$this->loader = new AnnotationLoader;
		$this->paser = new AnnotationParser;
	}

	/**
	 * 初始化
	 * @return void
	 */
	public function init()
	{
		// 框架
		$this->loadModuleAnnotations('Imi');
		// 应用
		$this->loadModuleAnnotations(App::getNamespace());
		// 服务器
		$config = MainHelper::getMain(App::getNamespace())->getConfig();
		if(isset($config['subServers']))
		{
			foreach($config['subServers'] as $item)
			{
				$this->loadModuleAnnotations($item['namespace']);
			}
		}
	}

	/**
	 * 获取原始数据
	 * @return array
	 */
	public function getData()
	{
		return $this->paser->getData();
	}

	/**
	 * 加载模块注解
	 * @param string $namespace
	 * @return void
	 */
	private function loadModuleAnnotations($namespace)
	{
		$this->loader->loadModuleAnnotations($namespace, function($fileNamespace){
			$this->paser->parse($fileNamespace);
		});
	}
}