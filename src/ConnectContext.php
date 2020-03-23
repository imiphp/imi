<?php
namespace Imi;

use Imi\RequestContext;

abstract class ConnectContext
{
    /**
     * 为当前连接创建上下文
     * @return void
     */
    public static function create(array $data = [])
    {
        $data = static::get();
        if(!$data && $fd = RequestContext::get('fd'))
        {
            static::use(function($contextData) use($data, $fd){
                $contextData = $data;
                $contextData['fd'] = $fd;
                return $contextData;
            }, $fd);
        }
    }

    /**
     * 从某个连接上下文中，加载到当前上下文或指定上下文中
     *
     * @param integer $fromFd
     * @param integer|null $toFd
     * @return void
     */
    public static function load(int $fromFd, ?int $toFd = null)
    {
        if(!$toFd)
        {
            $toFd = RequestContext::get('fd');
        }
        $data = static::getContext($fromFd);
        static::use(function($contextData) use($data, $toFd){
            $contextData = $data;
            $contextData['fd'] = $toFd;
            return $contextData;
        }, $toFd);
    }

    /**
     * 销毁当前连接的上下文
     * 
     * @param int|null $fd
     * @return void
     */
    public static function destroy($fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        /** @var \Imi\Server\ConnectContext\StoreHandler $store */
        $store = RequestContext::getServerBean('ConnectContextStore');
        if(($ttl = $store->getTtl()) > 0)
        {
            $store->delayDestroy($fd, $ttl);
        }
        else
        {
            $store->destroy($fd);
        }
    }

    /**
     * 判断当前连接上下文是否存在
     * @param int|null $fd
     * @return boolean
     */
    public static function exists($fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        return RequestContext::getServerBean('ConnectContextStore')->exists($fd);
    }

    /**
     * 获取上下文数据
     * @param string|null $name
     * @param mixed $default
     * @param int|null $fd
     * @return mixed
     */
    public static function get($name = null, $default = null, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $data = RequestContext::getServerBean('ConnectContextStore')->read($fd);
        if(null === $name)
        {
            return $data;
        }
        else
        {
            return $data[$name] ?? $default;
        }
    }

    /**
     * 设置上下文数据
     * @param string $name
     * @param mixed $value
     * @param int|null $fd
     * @return void
     */
    public static function set($name, $value, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $result = $store->lock($fd, function() use($store, $name, $value, $fd){
            $data = $store->read($fd);
            $data[$name] = $value;
            $store->save($fd, $data);
        });
        if(!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 批量设置上下文数据
     *
     * @param array $data
     * @param int|null $fd
     * @return void
     */
    public static function muiltiSet(array $data, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $result = $store->lock($fd, function() use($store, $data, $fd){
            $storeData = $store->read($fd);
            foreach($data as $name => $value)
            {
                $storeData[$name] = $value;
            }
            $store->save($fd, $storeData);
        });
        if(!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文
     *
     * @param callable $callable
     * @param int|null $fd
     * @return void
     */
    public static function use($callable, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $store->lock($fd, function() use($callable, $store, $fd){
            $data = $store->read($fd);
            $result = $callable($data);
            if($result)
            {
                $store->save($fd, $result);
            }
        });
    }

    /**
     * 获取当前上下文
     * @param int|null $fd
     * @return array
     */
    public static function getContext($fd = null)
    {
        return static::get(null, null, $fd);
    }

    /**
     * 绑定一个标记到当前连接
     *
     * @param string $flag
     * @param integer|null $fd
     * @return void
     */
    public static function bind(string $flag, ?int $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $connectionBinder->bind($flag, $fd);
    }

    /**
     * 取消绑定
     *
     * @param string $flag
     * @return void
     */
    public static function unbind(string $flag)
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $connectionBinder->unbind($flag);
    }

    /**
     * 使用标记获取连接编号
     *
     * @param string $flag
     * @return int|null
     */
    public static function getFdByFlag(string $flag): ?int
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        return $connectionBinder->getFdByFlag($flag);
    }

    /**
     * 恢复标记对应连接中的数据
     *
     * @param string $flag
     * @param integer|null $toFd
     * @return void
     */
    public static function restore(string $flag, ?int $toFd = null)
    {
        $fromFd = static::getFdByFlag($flag);
        if(!$fromFd)
        {
            throw new \RuntimeException(sprintf('Not found fd of connection flag %s', $flag));
        }
        static::load($fromFd, $toFd);
    }

}