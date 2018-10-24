<?php
namespace Imi\Server\Session;

use Imi\Util\Random;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use Imi\Server\Session\Handler\ISessionHandler;
use Imi\Util\ObjectArrayHelper;

/**
 * @Bean("SessionManager")
 */
class SessionManager
{
    /**
     * Session配置
     * @Inject("SessionConfig")
     * @var \Imi\Server\Session\SessionConfig
     */
    protected $config;

    /**
     * 是否已经启动
     * @var boolean
     */
    private $isStart = false;

    /**
     * Session处理对象
     * @var \Imi\Server\Session\Handler\ISessionHandler
     */
    private $handler;

    /**
     * session id
     * @var string
     */
    private $id;

    /**
     * Session 数据
     * @var array
     */
    private $data = [];

    /**
     * 是否对Session数据有修改
     *
     * @var boolean
     */
    private $isChanged = false;

    /**
     * 当前是否是新的会话
     *
     * @var boolean
     */
    private $isNewSession;

    /**
     * Session处理类
     * @var string
     */
    protected $handlerClass = \Imi\Server\Session\Handler\File::class;

    public function __construct(SessionConfig $config = null)
    {
        if(null !== $config)
        {
            $this->config = $config;
        }
    }

    /**
     * 开启session
     * @return void
     */
    public function start(string $sessionID = null)
    {
        if($this->isStart)
        {
            throw new \RuntimeException('Session can not repeated start');
        }
        $this->handler = RequestContext::getServerBean($this->handlerClass);
        $this->isNewSession = null === $sessionID;
        if($this->isNewSession)
        {
            $this->id = $this->handler->createSessionID();
        }
        else
        {
            $this->id = $sessionID;
            $this->data = $this->handler->decode($this->handler->read($this->id));
        }
        $this->isStart = true;
    }

    /**
     * 关闭session
     * @return void
     */
    public function close()
    {
        $this->data = [];
        $this->isStart = false;
    }

    /**
     * 销毁session
     * @return void
     */
    public function destroy()
    {
        $this->handler->destroy($this->id);
        $this->isStart = false;
    }

    /**
     * 保存session
     * @return void
     */
    public function save()
    {
        $this->handler->write($this->id, $this->handler->encode($this->data), $this->config->maxLifeTime);
    }

    /**
     * 保存并关闭session
     * @return void
     */
    public function commit()
    {
        if($this->isChanged)
        {
            $this->save();
        }
        $this->close();
    }

    /**
     * 是否已开启session
     * @return boolean
     */
    public function isStart()
    {
        return $this->isStart;
    }

    /**
     * 获取session name
     * @return string
     */
    public function getName()
    {
        return $this->config->name;
    }

    /**
     * 获取session id
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * 获取Session处理器
     * @return ISessionHandler
     */
    public function getHandler(): ISessionHandler
    {
        return $this->handler;
    }

    /**
     * 按概率进行尝试垃圾回收
     * @return void
     */
    public function tryGC()
    {
        if(Random::number(0, 1) <= $this->config->gcProbability)
        {
            $this->gc();
        }
    }

    /**
     * 垃圾回收
     * @return void
     */
    public function gc()
    {
        $this->handler->gc($this->config->maxLifeTime);
    }

    /**
     * 获取Session值
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        if(null === $name)
        {
            return $this->data;
        }
        $name = $this->parseName($name);
        return ObjectArrayHelper::get($this->data, $name, $default);
    }

    /**
     * 设置Session值
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value)
    {
        $name = $this->parseName($name);
        ObjectArrayHelper::set($this->data, $name, $value);
        $this->isChanged = true;
    }

    /**
     * 删除Session值
     * @param string $name
     * @return void
     */
    public function delete($name)
    {
        $name = $this->parseName($name);
        ObjectArrayHelper::remove($this->data, $name);
        $this->isChanged = true;
    }

    /**
     * 获取一次值后将该值删除，可用于验证码等一次性功能
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function once($name, $default = null)
    {
        $name = $this->parseName($name);
        $value = $this->get($name, $default);
        $this->delete($name);
        $this->isChanged = true;
        return $value;
    }
    
    /**
     * 清空所有Session
     * @param string $name
     * @return void
     */
    public function clear()
    {
        $this->data = [];
        $this->isChanged = true;
    }

    /**
     * 获取session配置
     * @return SessionConfig
     */
    public function getConfig(): SessionConfig
    {
        return $this->config;
    }

    /**
     * 处理name名称，@替换为前缀
     * @param string $name
     * @return string
     */
    public function parseName($name)
    {
        if(null !== $this->config->prefix)
        {
            return str_replace('@', $this->config->prefix, $name);
        }
        else
        {
            return $name;
        }
    }
    
    /**
     * 是否修改了Session数据
     *
     * @return boolean
     */
    public function isChanged()
    {
        return $this->isChanged;
    }

    /**
     * 当前是否是新的会话
     *
     * @return boolean
     */ 
    public function isNewSession()
    {
        return $this->isNewSession;
    }

}