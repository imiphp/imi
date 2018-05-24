<?php
namespace Imi;

use Imi\Main\BaseMain;

/**
 * 主类
 */
class Main extends BaseMain
{
	public function __init()
	{
	}

	/**
	 * 获取要扫描的bean的命名空间
	 * @return array
	 */
	public function getBeanScan(): array
	{
		return [
			'Imi\Bean',
			'Imi\Annotation',
			'Imi\Server',
			'Imi\Log',
			'Imi\Listener',
		];
	}

	/**
	 * 获取要初始化的原子计数名称
	 * @return array
	 */
	public function getAtomics(): array
	{
		return [
			'session'
		];
	}
}