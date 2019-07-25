<?php
namespace Imi\Cache\Handler;

use Imi\Util\Coroutine;
use Imi\Bean\Annotation\Bean;
use Imi\Util\File as FileUtil;
use Imi\Util\Stream\StreamMode;
use Imi\Util\DateTime;
use Imi\Config;

/**
 * @Bean("FileCache")
 */
class File extends Base
{
    /**
     * 缓存文件保存路径
     * @var string
     */
    protected $savePath;

    /**
     * 缓存文件名的处理回调，用于需要自定义的情况
     * @var callable
     */
    protected $saveFileNameCallback;

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        // 缓存文件不存在
        if(!is_file($fileName))
        {
            return $default;
        }
        $fp = fopen($fileName, StreamMode::READONLY);
        // 文件打开失败
        if (false === $fp)
        {
            return $default;
        }
        // 加锁失败
        if (!flock($fp, LOCK_SH))
        {
            fclose($fp);
            return $default;
        }
        // 检查是否过期
        if($this->checkExpire($fileName))
        {
            flock($fp, LOCK_UN);
            fclose($fp);
            return $default;
        }
        // 正常读入
        if(Coroutine::isIn() && !(method_exists('\Swoole\Runtime', 'enableCoroutine') && Config::get('@app.enableCoroutine', true)))
        {
            $content = Coroutine::fread($fp);
        }
        else
        {
            $content = FileUtil::readAll($fp);
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return $this->decode($content);
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        // 自动建目录
        $dir = dirname($fileName);
        if(!is_dir($dir))
        {
            mkdir($dir, 0755, true);
        }
        // 打开文件
        $fp = fopen($fileName, StreamMode::WRITE_CLEAN);
        if (false === $fp)
        {
            return false;
        }
        // 加锁失败
        if (!flock($fp, LOCK_EX))
        {
            fclose($fp);
            return false;
        }
        // 写入缓存数据
        if(Coroutine::isIn() && !(method_exists('\Swoole\Runtime', 'enableCoroutine') && Config::get('@app.enableCoroutine', true)))
        {
            Coroutine::fwrite($fp, $this->encode($value));
        }
        else
        {
            fwrite($fp, $this->encode($value));
        }
        // ttl 支持 \DateInterval 格式
        if($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        // 写入扩展数据
        $this->writeExData($fileName, $ttl);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        if(is_file($fileName))
        {
            unlink($fileName);
            $fileName = $this->getExDataFileName($key);
            if(is_file($fileName))
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
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        foreach(FileUtil::enumAll($this->savePath) as $fileName)
        {
            if(is_file($fileName))
            {
                unlink($fileName);
            }
            else if(is_dir($fileName))
            {
                rmdir($fileName);
            }
        }
        return true;
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        $this->checkArrayOrTraversable($keys);
        $result = [];
        foreach($keys as $key)
        {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->checkArrayOrTraversable($values);
        $result = true;
        // ttl 支持 \DateInterval 格式
        if($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        foreach($values as $key => $value)
        {
            $result = $result && $this->set($key, $value, $ttl);
        }
        return $result;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        $this->checkArrayOrTraversable($keys);
        $result = true;
        foreach($keys as $key)
        {
            $result = $result && $this->delete($key);
        }
        return $result;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key)
    {
        $this->checkKey($key);
        $fileName = $this->getFileName($key);
        // 缓存文件不存在
        if(!is_file($fileName))
        {
            return false;
        }
        $fp = fopen($fileName, StreamMode::READONLY);
        // 文件打开失败
        if (false === $fp)
        {
            return false;
        }
        // 加锁失败
        if (!flock($fp, LOCK_SH))
        {
            fclose($fp);
            return false;
        }
        $result = !$this->checkExpire($fileName);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $result;
    }

    /**
     * 获取缓存文件名完整路径
     * @param string $key
     * @return string
     */
    public function getFileName($key)
    {
        if(is_callable($this->saveFileNameCallback))
        {
            // 使用回调处理
            return ($this->saveFileNameCallback)($this->savePath, $key);
        }
        else
        {
            // 默认处理使用MD5
            return FileUtil::path($this->savePath, md5($key));
        }
    }

    /**
     * 获取存储扩展数据的文件名
     * @param string $fileName
     * @return string
     */
    public function getExDataFileName($fileName)
    {
        return $fileName . '.ex';
    }

    /**
     * 检查缓存文件是否过期
     * @param string $fileName
     * @return boolean
     */
    protected function checkExpire($fileName)
    {
        if(!is_file($fileName))
        {
            return false;
        }
        $exDataFileName = $this->getExDataFileName($fileName);
        $data = unserialize(file_get_contents($exDataFileName));
        if(null === $data['ttl'] ?? null)
        {
            return false;
        }
        $maxTime = time() - $data['ttl'];
        if(filemtime($fileName) <= $maxTime)
        {
            unlink($fileName);
            unlink($exDataFileName);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 写入扩展数据
     * @param string $fileName
     * @param int $ttl
     * @return void
     */
    protected function writeExData($fileName, $ttl)
    {
        $data = [
            'ttl' => $ttl,
        ];
        file_put_contents($this->getExDataFileName($fileName), serialize($data));
    }
}