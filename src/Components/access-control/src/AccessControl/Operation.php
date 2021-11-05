<?php

declare(strict_types=1);

namespace Imi\AC\AccessControl;

use Imi\App;

abstract class Operation
{
    /**
     * 创建操作权限.
     */
    public static function create(string $name, ?string $code = null, int $parentId = 0, int $index = 0, string $description = ''): \Imi\AC\Model\Operation
    {
        // @phpstan-ignore-next-line
        return App::getBean('ACOperationService')->create($name, $code, $parentId, $index, $description);
    }

    /**
     * 修改操作权限.
     */
    public static function update(int $id, string $name, ?string $code, int $parentId = 0, int $index = 0, string $description = ''): bool
    {
        // @phpstan-ignore-next-line
        return App::getBean('ACOperationService')->update($id, $name, $code, $parentId, $index, $description);
    }

    /**
     * 删除操作权限.
     */
    public static function delete(int $id): bool
    {
        // @phpstan-ignore-next-line
        return App::getBean('ACOperationService')->delete($id);
    }

    /**
     * 查询列表.
     */
    public static function selectList(): array
    {
        // @phpstan-ignore-next-line
        return App::getBean('ACOperationService')->selectList();
    }

    /**
     * 转为树形.
     */
    public static function listToTree(array $list): array
    {
        // @phpstan-ignore-next-line
        return App::getBean('ACOperationService')->listToTree($list);
    }
}
