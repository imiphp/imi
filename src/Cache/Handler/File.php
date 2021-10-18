<?php

declare(strict_types=1);

namespace Imi\Cache\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\DateTime;
use Imi\Util\File as FileUtil;
use Imi\Util\Stream\StreamMode;

/**
 * @Bean("FileCache")
 */
class File extends Base
{
    /**
     * 缓存文件保存路径.
     */
    protected string $savePath = '';

    /**
     * 缓存文件名的处理回调，用于需要自定义的情况.
     *
     * @var callable|null
     */
    protected $saveFileNameCallback;

    /**
     * 使用键名作为文件名.
     *
     * 如果设置了 $saveFileNameCallback 则 $keyAsFileName 无效
     */
    protected bool $keyAsFileName = false;

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        // 缓存文件不存在
        if (!is_file($fileName))
        {
            return $default;
        }
        $fp = fopen($fileName, StreamMode::READONLY);
        // 文件打开失败
        if (false === $fp)
        {
            return $default;
        }
        $isLocked = $isExpired = false;
        try
        {
            // 加锁失败
            if (!$isLocked = flock($fp, \LOCK_SH))
            {
                return $default;
            }
            // 检查是否过期
            if ($isExpired = $this->checkExpire($fileName))
            {
                return $default;
            }
            // 正常读入
            $content = FileUtil::readAll($fp);

            return $this->decode($content);
        }
        finally
        {
            if ($isLocked)
            {
                flock($fp, \LOCK_UN);
            }
            fclose($fp);
            if ($isExpired)
            {
                unlink($fileName);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        // 自动建目录
        $dir = \dirname($fileName);
        if (!is_dir($dir))
        {
            mkdir($dir, 0755, true);
        }
        // 打开文件
        $fp = fopen($fileName, StreamMode::WRITE_CLEAN);
        if (false === $fp)
        {
            return false;
        }
        $isLocked = false;
        try
        {
            // 加锁失败
            if (!$isLocked = flock($fp, \LOCK_EX))
            {
                return false;
            }
            // 写入缓存数据
            fwrite($fp, $this->encode($value));
            // ttl 支持 \DateInterval 格式
            if ($ttl instanceof \DateInterval)
            {
                $ttl = DateTime::getSecondsByInterval($ttl);
            }
            // 写入扩展数据
            $this->writeExData($fileName, $ttl);

            return true;
        }
        finally
        {
            if ($isLocked)
            {
                flock($fp, \LOCK_UN);
            }
            fclose($fp);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        if (is_file($fileName))
        {
            unlink($fileName);
            $fileName = $this->getExDataFileName($key);
            if (is_file($fileName))
            {
                unlink($fileName);
            }

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        foreach (FileUtil::enumAll($this->savePath) as $fileIterator)
        {
            $fileName = (string) $fileIterator;
            if (is_file($fileName))
            {
                unlink($fileName);
            }
            elseif (is_dir($fileName))
            {
                rmdir($fileName);
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->checkArrayOrTraversable($keys);
        $result = [];
        foreach ($keys as $key)
        {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->checkArrayOrTraversable($values);
        $result = true;
        // ttl 支持 \DateInterval 格式
        if ($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        foreach ($values as $key => $value)
        {
            $result = $result && $this->set($key, $value, $ttl);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple($keys)
    {
        $this->checkArrayOrTraversable($keys);
        $result = true;
        foreach ($keys as $key)
        {
            $result = $result && $this->delete($key);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        // 缓存文件不存在
        if (!is_file($fileName))
        {
            return false;
        }
        $fp = fopen($fileName, StreamMode::READONLY);
        // 文件打开失败
        if (false === $fp)
        {
            return false;
        }
        $isLocked = $isExpired = false;
        try
        {
            // 加锁失败
            if (!$isLocked = flock($fp, \LOCK_SH))
            {
                return false;
            }

            return !$isExpired = $this->checkExpire($fileName);
        }
        finally
        {
            if ($isLocked)
            {
                flock($fp, \LOCK_UN);
            }
            fclose($fp);
            if ($isExpired)
            {
                unlink($fileName);
            }
        }
    }

    /**
     * 获取缓存文件名完整路径.
     */
    public function getFileName(string $key): string
    {
        if (\is_callable($this->saveFileNameCallback))
        {
            // 使用回调处理
            return ($this->saveFileNameCallback)($this->savePath, $key);
        }
        elseif ($this->keyAsFileName)
        {
            return FileUtil::path($this->savePath, $key);
        }
        else
        {
            // 默认处理使用MD5
            return FileUtil::path($this->savePath, md5($key));
        }
    }

    /**
     * 获取存储扩展数据的文件名.
     */
    public function getExDataFileName(string $fileName): string
    {
        return $fileName . '.ex';
    }

    /**
     * 检查缓存文件是否过期
     */
    protected function checkExpire(string $fileName): bool
    {
        if (!is_file($fileName))
        {
            return false;
        }
        $exDataFileName = $this->getExDataFileName($fileName);
        if (!is_file($exDataFileName))
        {
            return false;
        }
        $data = unserialize(file_get_contents($exDataFileName));
        if (null === ($data['ttl'] ?? null))
        {
            return false;
        }
        $maxTime = time() - $data['ttl'];
        $filemtime = filemtime($fileName);
        if (false !== $filemtime && $filemtime <= $maxTime)
        {
            unlink($exDataFileName);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 写入扩展数据.
     */
    protected function writeExData(string $fileName, ?int $ttl): void
    {
        file_put_contents($this->getExDataFileName($fileName), serialize([
            'ttl' => $ttl,
        ]));
    }
}
