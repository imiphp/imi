<?php
namespace Imi\HotUpdate\Monitor;

use Imi\Util\File;


class FileMTime extends BaseMonitor
{
	private $files = [];

	private $excludeRule;

	/**
	 * 初始化
	 * @return void
	 */
	protected function init()
	{
		foreach($this->excludePaths as $i => $path)
		{
			$this->excludePaths[$i] = realpath($path);
		}
		$this->excludeRule = implode('|', array_map('\Imi\Util\Imi::parseRule', $this->excludePaths));
		foreach($this->includePaths as $i => $path)
		{
			$this->includePaths[$i] = $path = realpath($path);
			$directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
			$iterator = new \RecursiveIteratorIterator($directory);
			if('' === $this->excludeRule)
			{
				foreach($iterator as $fileName => $fileInfo)
				{
					$this->parseInitFile($fileName);
				}
			}
			else
			{
				$rule = "/^(?!{$this->excludeRule}).+$/i";
				$regex = new \RegexIterator($iterator, $rule, \RecursiveRegexIterator::GET_MATCH);
				foreach ($regex as $item)
				{
					$this->parseInitFile($item[0]);
				}
			}
		}
	}

	/**
	 * 处理初始化文件
	 *
	 * @param string $fileName
	 * @return void
	 */
	protected function parseInitFile($fileName)
	{
		$this->files[$fileName] = [
			'exists'	=>	true,
			'mtime'		=>	filemtime($fileName),
		];
	}

	/**
	 * 检测文件是否有更改
	 * @return boolean
	 */
	public function isChanged(): bool
	{
		$changed = false;
		$this->files = array_map(function($item){
			$item['exists'] = false;
			return $item;
		}, $this->files);
		// 包含的路径中检测
		foreach($this->includePaths as $path)
		{
			$directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS);
			$iterator = new \RecursiveIteratorIterator($directory);
			if('' === $this->excludeRule)
			{
				// 无排除规则处理
				foreach($iterator as $fileName => $fileInfo)
				{
					if($this->parseCheckFile($fileName))
					{
						$changed = true;
					}
				}
			}
			else
			{
				// 有排除规则处理
				$rule = "/^(?!{$this->excludeRule}).+$/i";
				$regex = new \RegexIterator($iterator, $rule, \RecursiveRegexIterator::GET_MATCH);
				foreach ($regex as $item)
				{
					if($this->parseCheckFile($item[0]))
					{
						$changed = true;
					}
				}
			}
		}
		// 之前有的文件被删处理
		foreach($this->files as $fileName => $option)
		{
			if(!$option['exists'])
			{
				unset($this->files[$fileName]);
				$changed = true;
			}
		}
		return $changed;
	}

	/**
	 * 处理检查文件是否更改，返回是否更改
	 * @param string $fileName
	 * @return bool
	 */
	protected function parseCheckFile($fileName)
	{
		$changed = false;
		$mtime = filemtime($fileName);
		if(isset($this->files[$fileName]))
		{
			// 判断文件修改时间
			if($this->files[$fileName]['mtime'] !== $mtime)
			{
				$changed = true;
			}
		}
		else
		{
			$changed = true;
		}
		$this->files[$fileName] = [
			'exists'	=>	true,
			'mtime'		=>	$mtime,
		];
		return $changed;
	}
	
}