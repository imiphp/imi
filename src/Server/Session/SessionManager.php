<?php

declare(strict_types=1);

namespace Imi\Server\Session;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Session\Handler\ISessionHandler;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Random;

/**
 * @Bean("SessionManager")
 */
class SessionManager
{
    /**
     * Session配置.
     *
     * @ServerInject("SessionConfig")
     *
     * @var \Imi\Server\Session\SessionConfig
     */
    protected SessionConfig $config;

    /**
     * 是否已经启动.
     */
    private bool $isStart = false;

    /**
     * Session处理对象
     */
    private ISessionHandler $handler;

    /**
     * session id.
     */
    private string $id = '';

    /**
     * Session 数据.
     */
    private array $data = [];

    /**
     * 是否对Session数据有修改.
     */
    private bool $isChanged = false;

    /**
     * 当前是否是新的会话.
     */
    private bool $isNewSession = false;

    /**
     * Session处理类.
     */
    protected string $handlerClass = \Imi\Server\Session\Handler\File::class;

    public function __construct(SessionConfig $config = null)
    {
        if (null !== $config)
        {
            $this->config = $config;
        }
    }

    /**
     * 开启session.
     */
    public function start(?string $sessionId = null): void
    {
        if ($this->isStart)
        {
            throw new \RuntimeException('Session can not repeated start');
        }
        $this->handler = $handler = RequestContext::getServerBean($this->handlerClass);
        $this->isNewSession = $isNewSession = null === $sessionId;
        if ($isNewSession)
        {
            $this->id = $handler->createSessionId();
        }
        else
        {
            $data = $handler->read($sessionId);
            if (false === $data)
            {
                $this->start(null);
            }
            else
            {
                $this->id = $sessionId;
                $this->data = $handler->decode($data);
            }
        }
        $this->isStart = true;
    }

    /**
     * 关闭session.
     */
    public function close(): void
    {
        $this->data = [];
        $this->isStart = false;
    }

    /**
     * 销毁session.
     */
    public function destroy(): void
    {
        $this->handler->destroy($this->id);
        $this->isStart = false;
    }

    /**
     * 保存session.
     */
    public function save(): void
    {
        $handler = $this->handler;
        $handler->write($this->id, $handler->encode($this->data), $this->config->maxLifeTime);
    }

    /**
     * 保存并关闭session.
     */
    public function commit(): void
    {
        if ($this->isChanged)
        {
            $this->save();
        }
        $this->close();
    }

    /**
     * 是否已开启session.
     *
     * @return bool
     */
    public function isStart()
    {
        return $this->isStart;
    }

    /**
     * 获取session name.
     */
    public function getName(): string
    {
        return $this->config->name;
    }

    /**
     * 获取session id.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * 获取Session处理器.
     */
    public function getHandler(): ISessionHandler
    {
        return $this->handler;
    }

    /**
     * 按概率进行尝试垃圾回收.
     */
    public function tryGC(): void
    {
        if (Random::number(0, 1) <= $this->config->gcProbability)
        {
            $this->gc();
        }
    }

    /**
     * 垃圾回收.
     */
    public function gc(): void
    {
        $this->handler->gc($this->config->maxLifeTime);
    }

    /**
     * 获取Session值
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(?string $name = null, $default = null)
    {
        if (null === $name)
        {
            return $this->data;
        }
        $name = $this->parseName($name);

        return ObjectArrayHelper::get($this->data, $name, $default);
    }

    /**
     * 设置Session值
     *
     * @param mixed $value
     */
    public function set(string $name, $value): void
    {
        $name = $this->parseName($name);
        ObjectArrayHelper::set($this->data, $name, $value);
        $this->isChanged = true;
    }

    /**
     * 删除Session值
     */
    public function delete(string $name): void
    {
        $name = $this->parseName($name);
        ObjectArrayHelper::remove($this->data, $name);
        $this->isChanged = true;
    }

    /**
     * 获取一次值后将该值删除，可用于验证码等一次性功能.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function once(string $name, $default = null)
    {
        $name = $this->parseName($name);
        $value = $this->get($name, $default);
        $this->delete($name);
        $this->isChanged = true;

        return $value;
    }

    /**
     * 清空所有Session.
     */
    public function clear(): void
    {
        $this->data = [];
        $this->isChanged = true;
    }

    /**
     * 获取session配置.
     */
    public function getConfig(): SessionConfig
    {
        return $this->config;
    }

    /**
     * 处理name名称，@替换为前缀
     */
    public function parseName(string $name): string
    {
        if (null !== $this->config->prefix)
        {
            return str_replace('@', $this->config->prefix, $name);
        }
        else
        {
            return $name;
        }
    }

    /**
     * 是否修改了Session数据.
     */
    public function isChanged(): bool
    {
        return $this->isChanged;
    }

    /**
     * 当前是否是新的会话.
     */
    public function isNewSession(): bool
    {
        return $this->isNewSession;
    }
}
