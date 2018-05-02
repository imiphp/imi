<?php
namespace Imi\Util;

abstract class File
{
	/**
	 * 枚举文件
	 * @param string $dirPath
	 * @return \RecursiveIterator
	 */
	public function enum($dirPath)
	{
		if(!is_dir($dirPath))
		{
			return;
		}
		$iterator = new \RecursiveDirectoryIterator($dirPath);
		$files = new \RecursiveIteratorIterator($iterator);
		foreach($files as $file)
		{
			yield $file;
		}
	}

	/**
	 * 枚举php文件
	 * @param string $dirPath
	 * @return \RegexIterator
	 */
	public function enumPHPFile($dirPath)
	{
		if(!is_dir($dirPath))
		{
			return;
		}
		$directory = new \RecursiveDirectoryIterator($dirPath);
		$iterator = new \RecursiveIteratorIterator($directory);
		$regex = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
		foreach($regex as $item)
		{
			yield $item[0];
		}
	}
}