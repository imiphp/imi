<?php

declare(strict_types=1);

namespace Imi\Core\Context;

use ArrayObject;
use Imi\Core\Context\Contract\IContextManager;
use Imi\Core\Context\Exception\ContextExistsException;
use Imi\Core\Context\Exception\ContextNotFoundException;

/**
 * 默认上下文管理器.
 */
class DefaultContextManager implements IContextManager
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
        if (isset($this->contexts[$flag]))
        {
            throw new ContextExistsException(sprintf('Context %s already exists!', $flag));
        }

        return $this->contexts[$flag] = new ArrayObject($data, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $flag): bool
    {
        if (isset($this->contexts[$flag]))
        {
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

    /**
     * {@inheritDoc}
     */
    public function exists(string $flag): bool
    {
        return isset($this->contexts[$flag]);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentFlag(): string
    {
        return 'default';
    }
}
