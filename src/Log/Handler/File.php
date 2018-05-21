<?php
namespace Imi\Log\Handler;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("FileLog")
 */
class File extends Base
{
	/**
	 * 要保存的文件名
	 * 支持使用date()函数中所有的格式，如经典年月日：logs/{y}-{m}-{d}.log
	 * 如果文件体积超出限制，会自动创建编号文件，如：第一个文件2018-01-01.log，第二个文件2018-01-01(1).log，第三个文件2018-01-01(2).log
	 * @var string
	 */
	protected $fileName;

	/**
	 * 单文件最大体积，单位字节，默认1G
	 * @var integer
	 */
	protected $maxSize = 1073741824;

	/**
	 * 当前保存的日志文件代表日期时间戳，精确到：小时
	 * @var int
	 */
	private $currentFileDateTime;

	/**
	 * 当前保存的日志文件名，不带序号，不带扩展名
	 * @var string
	 */
	private $currentBaseFileName;

	/**
	 * 当前保存的日志文件名，带序号
	 * @var string
	 */
	private $currentFileName;

	/**
	 * 当前文件序号
	 * @var integer
	 */
	private $currentFileIndex = 0;

	/**
	 * 当前日志文件扩展名
	 *
	 * @var string
	 */
	private $currentFileExt;

	/**
	 * 真正的保存操作实现
	 * @return void
	 */
    protected function __save()
    {
		$this->parseDate();
        foreach($this->records as $record)
        {
			\Swoole\Async::writeFile($this->getFileName(), $this->getLogString($record) . PHP_EOL, null, FILE_APPEND);
        }
	}
	
	/**
	 * 处理日期
	 * @return void
	 */
	private function parseDate()
	{
		$todayDateTime = strtotime(date('Y-m-d H:00:00'));
		if($todayDateTime !== $this->currentFileDateTime)
		{
			$this->getNewDateFileName($todayDateTime);
		}
	}

	/**
	 * 获取新日期的初始文件名
	 * @return string
	 */
	private function getNewDateFileName($timestamp)
	{
		$this->currentFileDateTime = $timestamp;
		$this->currentFileIndex = 0;
		$this->currentNoIndexFileName = $this->replaceDateTime($this->fileName, $timestamp);
		$this->currentFileExt = pathinfo($this->currentNoIndexFileName, PATHINFO_EXTENSION);
		return $this->currentNoIndexFileName = substr($this->currentNoIndexFileName, 0, -strlen($this->currentFileExt));
	}

	/**
	 * 获取当前日期的文件名
	 * @return string
	 */
	private function getFileName()
	{
		--$this->currentFileIndex;
		do{
			++$this->currentFileIndex;
			$fileName = $this->currentNoIndexFileName;
			if($this->currentFileIndex > 0)
			{
				$fileName .= '(' . $this->currentFileIndex . ')';
			}
			$fileName .= $this->currentFileExt;
		}while(filesize($fileName) >= $this->maxSize);
		return $fileName;
	}
}