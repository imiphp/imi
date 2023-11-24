<?php

declare(strict_types=1);

namespace Imi\Core\Context;

use Imi\Core\Context\Contract\IContextManager;
use Imi\Core\Context\Exception\ContextExistsException;
use Imi\Core\Context\Exception\ContextNotFoundException;
use Imi\Event\Event;

/**
 * 默认上下文管理器.
 */
class DefaultContextManager implements IContextManager
{
    /**
     * 上下文对象集合.
     *
     * @var \ArrayObject[]
     */
    private array $contexts = [];

    private bool $shutdownListened = false;

    /**
     * {@inheritDoc}
     */
    public function create(string $id, array $data = []): \ArrayObject
    {
        if (isset($this->contexts[$id]))
        {
            throw new ContextExistsException(sprintf('Context %s already exists!', $id));
        }

        // 脚本执行结束时自动销毁上下文
        if (!$this->shutdownListened)
        {
            $this->shutdownListened = true;
            $this->bindAutoDestroy();
        }

        return $this->contexts[$id] = new \ArrayObject($data, \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $id): bool
    {
        if (isset($this->contexts[$id]))
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

    /**
     * {@inheritDoc}
     */
    public function exists(string $id): bool
    {
        return isset($this->contexts[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentId(): string
    {
        return 'default';
    }

    protected function bindAutoDestroy(): void
    {
        register_shutdown_function(function (): void {
            if ($this->contexts)
            {
                foreach ($this->contexts as $id => $_)
                {
                    $this->destroy($id);
                }
            }
        });
    }
}
