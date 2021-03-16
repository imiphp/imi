<?php

declare(strict_types=1);

namespace Imi\Core\Context\Contract;

use ArrayObject;

interface IContextManager
{
    /**
     * 创建上下文.
     */
    public function create(string $flag, array $data = []): ArrayObject;

    /**
     * 销毁上下文.
     */
    public function destroy(string $flag): bool;

    /**
     * 获取上下文.
     */
    public function get(string $flag, bool $autoCreate = false): ArrayObject;

    /**
     * 上下文是否存在.
     */
    public function exists(string $flag): bool;

    /**
     * 获取当前上下文标识.
     */
    public function getCurrentFlag(): string;
}
