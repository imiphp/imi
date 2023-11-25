<?php

declare(strict_types=1);

namespace Imi\Core\Context\Contract;

use Imi\Core\Context\ContextData;

interface IContextManager
{
    /**
     * 创建上下文.
     */
    public function create(string|int $id, array $data = []): ContextData;

    /**
     * 销毁上下文.
     */
    public function destroy(string|int $id): bool;

    /**
     * 获取上下文.
     */
    public function get(string|int $id, bool $autoCreate = false): ContextData;

    /**
     * 上下文是否存在.
     */
    public function exists(string|int $id): bool;

    /**
     * 获取当前上下文标识.
     */
    public function getCurrentId(): string|int;
}
