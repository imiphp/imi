<?php

declare(strict_types=1);

namespace Imi\Swoole\Context;

use Imi\Core\Context\ContextData;
use Imi\Core\Context\Contract\IContextManager;
use Imi\Core\Context\Exception\ContextExistsException;
use Imi\Core\Context\Exception\ContextNotFoundException;
use Imi\Event\Event;
use Imi\Swoole\Util\Coroutine;

/**
 * Swoole 协程上下文管理器.
 */
class CoroutineContextManager implements IContextManager
{
    /**
     * 上下文对象集合.
     *
     * @var ContextData[]
     */
    private array $contexts = [];

    /**
     * {@inheritDoc}
     */
    public function create(string|int $id, array $data = []): ContextData
    {
        if ($id > -1)
        {
            $swooleContext = Coroutine::getContext((int) $id);
            // destroy
            if (!($swooleContext[static::class]['destroyBinded'] ?? false))
            {
                $swooleContext[static::class]['destroyBinded'] = true;
                Coroutine::defer(fn () => $this->destroy($id));
            }
            $context = $swooleContext[static::class]['context'] ?? null;
            if ($context)
            {
                if ($data)
                {
                    foreach ($data as $k => $v)
                    {
                        $context[$k] = $v;
                    }
                }
            }
            else
            {
                $context = $swooleContext[static::class]['context'] = new ContextData($data);
            }

            return $context;
        }
        else
        {
            if (isset($this->contexts[$id]))
            {
                throw new ContextExistsException(sprintf('Context %s already exists!', $id));
            }

            return $this->contexts[$id] = new ContextData($data);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string|int $id): bool
    {
        if ($id > -1)
        {
            $swooleContext = Coroutine::getContext((int) $id);
            if (!isset($swooleContext[static::class]['context']))
            {
                return false;
            }
            // TODO: 实现新的连接管理器后移除
            Event::trigger('IMI.REQUEST_CONTENT.DESTROY');
            /** @var ContextData $context */
            $context = $swooleContext[static::class]['context'];
            $deferCallbacks = $context->getDeferCallbacks();
            while (!$deferCallbacks->isEmpty())
            {
                $deferCallbacks->pop()();
            }
            unset($swooleContext[static::class]);

            return true;
        }
        elseif (isset($this->contexts[$id]))
        {
            // TODO: 实现新的连接管理器后移除
            Event::trigger('IMI.REQUEST_CONTENT.DESTROY');
            $deferCallbacks = $this->contexts[$id]->getDeferCallbacks();
            while (!$deferCallbacks->isEmpty())
            {
                $deferCallbacks->pop()();
            }
            unset($this->contexts[$id]);

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
    public function get(string|int $id, bool $autoCreate = false): ContextData
    {
        if ($id > -1)
        {
            $swooleContext = Coroutine::getContext((int) $id);
            // destroy
            if (!($swooleContext[static::class]['destroyBinded'] ?? false))
            {
                $swooleContext[static::class]['destroyBinded'] = true;
                Coroutine::defer(fn () => $this->destroy($id));
            }

            if (!isset($swooleContext[static::class]['context']))
            {
                if ($autoCreate)
                {
                    return $swooleContext[static::class]['context'] = new ContextData();
                }
                throw new ContextNotFoundException(sprintf('Context %s does not exists!', $id));
            }

            return $swooleContext[static::class]['context'];
        }
        else
        {
            if (!isset($this->contexts[$id]))
            {
                if ($autoCreate)
                {
                    return $this->create($id);
                }
                throw new ContextNotFoundException(sprintf('Context %s does not exists!', $id));
            }

            return $this->contexts[$id];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string|int $id): bool
    {
        if ($id > -1)
        {
            $swooleContext = Coroutine::getContext((int) $id);

            return $swooleContext && isset($swooleContext[static::class]['context']);
        }
        else
        {
            return isset($this->contexts[$id]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentId(): string|int
    {
        return (string) Coroutine::getCid();
    }
}
