<?php
namespace Imi\Server\Session\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\File as FileUtil;

/**
 * @Bean("SessionFile")
 */
class File extends Base
{
	/**
	 * Session文件存储路径
	 * @var string
	 */
	protected $savePath;

	/**
	 * 销毁session数据
	 * @param string $sessionID
	 * @return void
	 */
	public function destroy($sessionID)
	{
		$fileName = $this->getFileName($sessionID);
		if(is_file($fileName))
		{
			unlink($fileName);
		}
	}

	/**
	 * 垃圾回收
	 * @param string $maxLifeTime 最大存活时间
	 * @return void
	 */
	public function gc($maxLifeTime)
	{
		$files = new FilesystemIterator($this->savePath);
		$maxTime = time() - $maxLifeTime;
		foreach($files as $file)
		{
			$fileName = $file->getPathname();
			if(filemtime($fileName) <= $maxTime)
			{
				unlink($fileName);
			}
		}
	}

	/**
	 * 读取session
	 * @param string $sessionID
	 * @return mixed
	 */
	public function read($sessionID)
	{
		$fileName = $this->getFileName($sessionID);
		if(is_file($fileName))
		{
			return file_get_contents($fileName);
		}
		else
		{
			return '';
		}
	}

	/**
	 * 写入session
	 * @param string $sessionID
	 * @param string $sessionData
	 * @return void
	 */
	public function write($sessionID, $sessionData)
	{
		file_put_contents($this->getFileName($sessionID), $sessionData);
	}

	/**
	 * 获取文件存储的完整文件名
	 * @param string $sessionID
	 * @return string
	 */
	public function getFileName($sessionID)
	{
		return FileUtil::path($this->savePath, $sessionID . '.session');
	}
}