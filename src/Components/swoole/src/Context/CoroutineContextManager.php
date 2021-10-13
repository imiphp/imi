<?php

declare(strict_types=1);

namespace Imi\Swoole\Context;

use ArrayObject;
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
     * @var ArrayObject[]
     */
    private array $contexts = [];

    /**
     * {@inheritDoc}
     */
    public function create(string $flag, array $data = []): ArrayObject
    {
        if ($flag > -1)
        {
            $context = Coroutine::getContext((int) $flag);
            // destroy
            if (!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer([$this, '__destroy']);
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
            if (isset($this->contexts[$flag]))
            {
                throw new ContextExistsException(sprintf('Context %s already exists!', $flag));
            }

            return $this->contexts[$flag] = new ArrayObject($data, ArrayObject::ARRAY_AS_PROPS);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $flag): bool
    {
        if ($flag > -1)
        {
            return false; // 协程退出时自动销毁，无法手动销毁
        }
        elseif (isset($this->contexts[$flag]))
        {
            Event::trigger('IMI.REQUEST_CONTENT.DESTROY');
            unset($this->contexts[$flag]);

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
    public function get(string $flag, bool $autoCreate = false): ArrayObject
    {
        if ($flag > -1)
        {
            $context = Coroutine::getContext((int) $flag);
            // destroy
            if (!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer([$this, '__destroy']);
            }

            return $context;
        }
        else
        {
            if (!isset($this->contexts[$flag]))
            {
                if ($autoCreate)
                {
                    return $this->create($flag);
                }
                throw new ContextNotFoundException(sprintf('Context %s does not exists!', $flag));
            }

            return $this->contexts[$flag];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $flag): bool
    {
        if ($flag > -1)
        {
            return Coroutine::exists($flag);
        }
        else
        {
            return isset($this->contexts[$flag]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentFlag(): string
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
}
