<?php

declare(strict_types=1);

namespace Imi\Core\Context\Contract;

interface IContextManager
{
    /**
     * 创建上下文.
     */
    public function create(string $id, array $data = []): \ArrayObject;

    /**
     * 销毁上下文.
     */
    public function destroy(string $id): bool;

    /**
     * 获取上下文.
     */
    public function get(string $id, bool $autoCreate = false): \ArrayObject;

    /**
     * 上下文是否存在.
     */
    public function exists(string $id): bool;

    /**
     * 获取当前上下文标识.
     */
    public function getCurrentId(): string;
}
