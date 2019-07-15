<?php
namespace Imi;

use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Server\ConnectContext\Model\FdRelation;
use Imi\Server\Event\Param\PipeMessageEventParam;
use Imi\Server\ConnectContext\ConnectContextChangeEventParam;
use Imi\Event\Event;

abstract class ConnectContext
{
    /**
     * 上下文数据
     *
     * @var array
     */
    private static $context = [];

    /**
     * 事件回调们
     *
     * @var array
     */
    private static $eventCallables = [];

    /**
     * 为当前连接创建上下文
     * @return void
     */
    public static function create()
    {
        $fd = RequestContext::get('fd');
        if(!isset(static::$context[$fd]))
        {
            static::$context[$fd] = RequestContext::getServerBean('ConnectContextStore')->read($fd);
            static::registerChangeEvent($fd);
        }
    }

    /**
     * 销毁当前连接的上下文
     * 
     * @param int|null $fd
     * @return void
     */
    public static function destroy($fd = null)
    {
        if($fd)
        {
            $fdBeginNull = false;
        }
        else
        {
            $fd = RequestContext::get('fd');
            $fdBeginNull = true;
        }
        if(isset(static::$context[$fd]))
        {
            unset(static::$context[$fd]);
        }
        RequestContext::getServerBean('ConnectContextStore')->destroy($fd);
        if(!$fdBeginNull)
        {
            static::triggerChangeEvent($fd);
            static::unregisterChanageEvent($fd);
        }
    }

    /**
     * 判断当前连接上下文是否存在
     * @deprecated 1.0
     * @return boolean
     */
    public static function exsits()
    {
        return static::exists();
    }

    /**
     * 判断当前连接上下文是否存在
     * @param int|null $fd
     * @return boolean
     */
    public static function exists($fd = null)
    {
        if(RequestContext::exists())
        {
            if(!$fd)
            {
                $fd = RequestContext::get('fd');
            }
            return isset(static::$context[$fd]) || RequestContext::getServerBean('ConnectContextStore')->exists($fd);
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取上下文数据
     * @param string $name
     * @param mixed $default
     * @param int|null $fd
     * @return mixed
     */
    public static function get($name, $default = null, $fd = null)
    {
        if($fd)
        {
            return RequestContext::getServerBean('ConnectContextStore')->read($fd)[$name] ?? $default;
        }
        else
        {
            $fd = RequestContext::get('fd');
            if(!isset(static::$context[$fd]))
            {
                static::$context[$fd] = RequestContext::getServerBean('ConnectContextStore')->read($fd);
            }
            return static::$context[$fd][$name] ?? $default;
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
        $store = RequestContext::getServerBean('ConnectContextStore');
        
        if($fd)
        {
            $data = $store->read($fd);
            $data[$name] = $value;
            $store->save($fd, $data);
            static::triggerChangeEvent($fd);
        }
        else
        {
            $fd = RequestContext::get('fd');
            if(!isset(static::$context[$fd]))
            {
                static::$context[$fd] = $store->read($fd);
            }
            static::$context[$fd][$name] = $value;
            $store->save($fd, static::$context[$fd]);
        }
    }

    /**
     * 获取当前上下文
     * @param int|null $fd
     * @return array
     */
    public static function getContext($fd = null)
    {
        return static::$context[$fd] ?? null;
    }

    /**
     * 注册改变事件
     *
     * @param int $fd
     * @return void
     */
    private static function registerChangeEvent($fd)
    {
        if(isset(static::$eventCallables[$fd]))
        {
            return;
        }
        static::$eventCallables[$fd] = imiCallable(function(PipeMessageEventParam $param) use($fd) {
            if($param->message instanceof ConnectContextChangeEventParam && $fd == $param->message->getFd())
            {
                static::$context[$fd] = ServerManage::getServer($param->message->getServerName())->getBean('ConnectContextStore')->read($fd);
                $param->stopPropagation();
            }
        });
        $fdRelation = FdRelation::newInstance();
        $fdRelation->__setKey($fd);
        $fdRelation->setWorkerId(Worker::getWorkerID());
        $fdRelation->setServerName(RequestContext::getServer()->getName());
        $fdRelation->save();
        Event::on('IMI.MAIN_SERVER.PIPE_MESSAGE', static::$eventCallables[$fd]);
    }

    /**
     * 卸载改变事件
     *
     * @param int $fd
     * @return void
     */
    private static function unregisterChanageEvent($fd)
    {
        if(isset(static::$eventCallables[$fd]))
        {
            $fdRelation = FdRelation::find($fd);
            if($fdRelation)
            {
                $fdRelation->delete();
            }
            Event::off('IMI.MAIN_SERVER.PIPE_MESSAGE', static::$eventCallables[$fd]);
            unset(static::$eventCallables[$fd]);
        }
    }

    /**
     * 触发更改事件
     *
     * @param int $fd
     * @return void
     */
    private static function triggerChangeEvent($fd)
    {
        $fdRelation = FdRelation::find($fd);
        if(!$fdRelation)
        {
            return;
        }
        $message = new ConnectContextChangeEventParam($fd, $fdRelation->getServerName());
        $server = ServerManage::getServer($fdRelation->getServerName());
        if(Worker::getWorkerID() != $fdRelation->getWorkerId())
        {
            RequestContext::getServer()->getSwooleServer()->sendMessage($message, $fdRelation->getWorkerId());
        }
    }

}