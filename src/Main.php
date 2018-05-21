<?php
namespace Imi;

use Imi\Main\BaseMain;
use Imi\Util\TSingleton;

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
		];
	}
}