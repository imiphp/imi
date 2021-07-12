<?php

declare(strict_types=1);

namespace Imi\AC\Service;

use Imi\AC\Exception\OperationNotFound;
use Imi\AC\Model\Filter\OperationTreeItem;
use Imi\AC\Model\Operation;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("ACOperationService")
 */
class OperationService
{
    /**
     * 操作权限模型.
     */
    protected string $operationModel = Operation::class;

    /**
     * 获取操作.
     */
    public function get(int $id): ?Operation
    {
        return $this->operationModel::find($id);
    }

    /**
     * 创建操作权限.
     *
     * @return \Imi\AC\Model\Operation|false
     */
    public function create(string $name, ?string $code = null, int $parentId = 0, int $index = 0, string $description = '')
    {
        $record = $this->operationModel::newInstance();
        $record->name = $name;
        $record->code = $code ?? $name;
        $record->parentId = $parentId;
        $record->index = $index;
        $record->description = $description;
        $result = $record->insert();
        if (!$result->isSuccess())
        {
            return false;
        }

        return $record;
    }

    /**
     * 更新操作权限.
     */
    public function update(int $id, string $name, ?string $code, int $parentId = 0, int $index = 0, string $description = ''): bool
    {
        $record = $this->get($id);
        if (!$record)
        {
            throw new OperationNotFound(sprintf('Operation id = %s does not found', $id));
        }
        $record->name = $name;
        $record->code = $code;
        $record->parentId = $parentId;
        $record->index = $index;
        $record->description = $description;

        return $record->update()->isSuccess();
    }

    /**
     * 删除操作.
     */
    public function delete(int $id): bool
    {
        $record = $this->get($id);
        if (!$record)
        {
            throw new OperationNotFound(sprintf('Operation id = %s does not found', $id));
        }

        return $record->delete()->isSuccess();
    }

    /**
     * 根据代码获取角色.
     */
    public function getByCode(string $code): ?Operation
    {
        return $this->operationModel::query()->where('code', '=', $code)->select()->get();
    }

    /**
     * 根据多个角色获取操作ID.
     *
     * @return int[]
     */
    public function selectIdsByCodes(array $codes): array
    {
        if (!$codes)
        {
            return [];
        }

        return $this->operationModel::query()->field('id')->whereIn('code', $codes)->select()->getColumn();
    }

    /**
     * 根据id列表查询记录.
     *
     * @return \Imi\AC\Model\Operation[]
     */
    public function selectListByIds(array $ids): array
    {
        if (!$ids)
        {
            return [];
        }

        return $this->operationModel::query()->whereIn('id', $ids)
                                 ->order('index')
                                 ->select()
                                 ->getArray();
    }

    /**
     * 查询列表.
     *
     * @return \Imi\AC\Model\Operation[]
     */
    public function selectList(): array
    {
        return $this->operationModel::select();
    }

    /**
     * 转为树形.
     *
     * @param \Imi\AC\Model\Operation[] $list
     *
     * @return \Imi\AC\Model\Filter\OperationTreeItem[]
     */
    public function listToTree(array $list): array
    {
        $tree = [];

        // 查询出所有分类记录
        $arr2 = [];
        // 处理成ID为键名的数组
        foreach ($list as $item)
        {
            $arr2[$item->id] = OperationTreeItem::newInstance($item->toArray());
        }
        // 循环处理关联列表
        foreach ($arr2 as $item)
        {
            if (isset($arr2[$item->parentId]))
            {
                $arr2[$item->parentId]->children[] = $arr2[$item->id];
            }
            else
            {
                $tree[] = $arr2[$item->id];
            }
        }

        return $tree;
    }
}
