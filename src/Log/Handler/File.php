<?php

namespace Imi\Log\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\File as UtilFile;

/**
 * @Bean("FileLog")
 */
class File extends Base
{
    /**
     * 要保存的文件名
     * 支持使用date()函数中所有的格式，如经典年月日：logs/{y}-{m}-{d}.log
     * 如果文件体积超出限制，会自动创建编号文件，如：第一个文件2018-01-01.log，第二个文件2018-01-01(1).log，第三个文件2018-01-01(2).log.
     *
     * @var string
     */
    protected $fileName;

    /**
     * 单文件最大体积，单位字节，默认1G.
     *
     * @var int
     */
    protected $maxSize = 1073741824;

    /**
     * 当前保存的日志文件代表日期时间戳，精确到：小时.
     *
     * @var int
     */
    private $currentFileDateTime;

    /**
     * 当前文件序号.
     *
     * @var int
     */
    private $currentFileIndex = 0;

    /**
     * 当前日志文件扩展名.
     *
     * @var string
     */
    private $currentFileExt;

    /**
     * 当前无序号的文件名.
     *
     * @var string
     */
    private $currentNoIndexFileName;

    /**
     * 真正的保存操作实现.
     *
     * @return void
     */
    protected function __save()
    {
        $this->parseDate();
        foreach ($this->records as $record)
        {
            file_put_contents($this->getFileName(), $this->getLogString($record) . \PHP_EOL, \FILE_APPEND | \LOCK_NB);
        }
    }

    /**
     * 处理日期
     *
     * @return void
     */
    private function parseDate()
    {
        $todayDateTime = strtotime(date('Y-m-d H:00:00'));
        if (false !== $todayDateTime && $todayDateTime !== $this->currentFileDateTime)
        {
            $this->getNewDateFileName($todayDateTime);
        }
    }

    /**
     * 获取新日期的初始文件名.
     *
     * @param int $timestamp
     *
     * @return string
     */
    private function getNewDateFileName($timestamp)
    {
        $this->currentFileDateTime = $timestamp;
        $this->currentFileIndex = 0;
        $currentNoIndexFileName = $this->replaceDateTime($this->fileName, $timestamp);
        $this->currentFileExt = $currentFileExt = '.' . pathinfo($currentNoIndexFileName, \PATHINFO_EXTENSION);

        return $this->currentNoIndexFileName = substr($currentNoIndexFileName, 0, -\strlen($currentFileExt));
    }

    /**
     * 获取当前日期的文件名.
     *
     * @return string
     */
    private function getFileName()
    {
        $currentFileIndex = &$this->currentFileIndex;
        --$currentFileIndex;
        $currentFileExt = $this->currentFileExt;
        $maxSize = $this->maxSize;
        $currentNoIndexFileName = $this->currentNoIndexFileName;
        do
        {
            ++$currentFileIndex;
            $fileName = $currentNoIndexFileName;
            if ($currentFileIndex > 0)
            {
                $fileName .= '(' . $currentFileIndex . ')';
            }
            $fileName .= $currentFileExt;
        } while (is_file($fileName) && filesize($fileName) >= $maxSize);
        // 自动创建目录
        $dir = \dirname($fileName);
        if (!is_dir($dir))
        {
            UtilFile::createDir($dir);
        }

        return $fileName;
    }
}
