<?php
namespace Imi\Server\Session\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\File as FileUtil;
use Imi\Util\Coroutine;

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
     * 执行初始化操作
     */
    public function __init(){
        FileUtil::createDir($this->savePath);
    }

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
     * @param int $maxLifeTime 最大存活时间，单位：秒
     * @return void
     */
    public function gc($maxLifeTime)
    {
        $files = new \FilesystemIterator($this->savePath);
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
            return Coroutine::readFile($fileName);
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
     * @param string $maxLifeTime
     * @return void
     */
    public function write($sessionID, $sessionData, $maxLifeTime)
    {
        Coroutine::writeFile($this->getFileName($sessionID), $sessionData, LOCK_EX);
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