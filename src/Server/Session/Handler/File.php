<?php

declare(strict_types=1);

namespace Imi\Server\Session\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\File as FileUtil;

/**
 * @Bean("SessionFile")
 */
class File extends Base
{
    /**
     * Session文件存储路径.
     *
     * @var string
     */
    protected string $savePath = '';

    /**
     * 执行初始化操作.
     */
    public function __init()
    {
        parent::__init();
        FileUtil::createDir($this->savePath);
    }

    /**
     * 销毁session数据.
     *
     * @param string $sessionId
     *
     * @return void
     */
    public function destroy(string $sessionId)
    {
        $fileName = $this->getFileName($sessionId);
        if (is_file($fileName))
        {
            unlink($fileName);
        }
    }

    /**
     * 垃圾回收.
     *
     * @param int $maxLifeTime 最大存活时间，单位：秒
     *
     * @return void
     */
    public function gc(int $maxLifeTime)
    {
        $files = new \FilesystemIterator($this->savePath);
        $maxTime = time() - $maxLifeTime;
        foreach ($files as $file)
        {
            $fileName = $file->getPathname();
            if (filemtime($fileName) <= $maxTime)
            {
                unlink($fileName);
            }
        }
    }

    /**
     * 读取session.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public function read(string $sessionId): string
    {
        $fileName = $this->getFileName($sessionId);
        if (is_file($fileName))
        {
            return file_get_contents($fileName);
        }
        else
        {
            return '';
        }
    }

    /**
     * 写入session.
     *
     * @param string $sessionId
     * @param string $sessionData
     * @param int    $maxLifeTime
     *
     * @return void
     */
    public function write(string $sessionId, string $sessionData, int $maxLifeTime)
    {
        file_put_contents($this->getFileName($sessionId), $sessionData, \LOCK_EX);
    }

    /**
     * 获取文件存储的完整文件名.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public function getFileName(string $sessionId): string
    {
        return FileUtil::path($this->savePath, $sessionId . '.session');
    }
}
