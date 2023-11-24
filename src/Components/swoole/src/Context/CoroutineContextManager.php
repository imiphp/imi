<?php

declare(strict_types=1);

namespace Imi\Swoole\Context;

use Imi\Core\Context\Contract\IContextManager;
use Imi\Core\Context\Exception\ContextExistsException;
use Imi\Core\Context\Exception\ContextNotFoundException;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Swoole\Util\Coroutine;

/**
 * Swoole 协程上下文管理器.
 */
class CoroutineContextManager implements IContextManager
{
    /**
     * 上下文对象集合.
     *
     * @var \ArrayObject[]
     */
    private array $contexts = [];

    /**
     * {@inheritDoc}
     */
    public function create(string $id, array $data = []): \ArrayObject
    {
        if ($id > -1)
        {
            $context = Coroutine::getContext((int) $id);
            // destroy
            if (!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer($this->__destroy(...));
            }
            if ($data)
            {
                foreach ($data as $k => $v)
                {
                    $context[$k] = $v;
                }
            }

            return $context;
        }
        else
        {
            if (isset($this->contexts[$id]))
            {
                throw new ContextExistsException(sprintf('Context %s already exists!', $id));
            }

            return $this->contexts[$id] = new \ArrayObject($data, \ArrayObject::ARRAY_AS_PROPS);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $id): bool
    {
        if ($id > -1)
        {
            return false; // 协程退出时自动销毁，无法手动销毁
        }
        elseif (isset($this->contexts[$id]))
        {
            Event::trigger('IMI.REQUEST_CONTENT.DESTROY');
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
    public function get(string $id, bool $autoCreate = false): \ArrayObject
    {
        if ($id > -1)
        {
            $context = Coroutine::getContext((int) $id);
            // destroy
            if (!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer($this->__destroy(...));
            }

            return $context;
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
    public function exists(string $id): bool
    {
        if ($id > -1)
        {
            return Coroutine::exists($id);
        }
        else
        {
            return isset($this->contexts[$id]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentId(): string
    {
        return (string) Coroutine::getCid();
    }

    /**
     * 销毁当前请求的上下文.
     *
     * 不要手动调用！不要手动调用！不要手动调用！
     */
    public function __destroy(): void
    {
        try
        {
            Event::trigger('IMI.REQUEST_CONTENT.DESTROY');
            $context = Coroutine::getContext();
            if (!$context)
            {
                $coId = Coroutine::getCid();
                $contextMap = &$this->contextMap;
                if (isset($contextMap[$coId]))
                {
                    unset($contextMap[$coId]);
                }
            }
        }
        catch (\Throwable $th)
        {
            Log::error($th);
        }
    }
}
