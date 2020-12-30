<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IJoin extends IBase
{
    /**
     * 表名.
     *
     * @return string|null
     */
    public function getTable(): ?string;

    /**
     * 在 join b on a.id=b.id 中的 a.id.
     *
     * @return string|null
     */
    public function getLeft(): ?string;

    /**
     * 在 join b on a.id=b.id 中的 =.
     *
     * @return string|null
     */
    public function getOperation(): ?string;

    /**
     * join b on a.id=b.id 中的 b.id.
     *
     * @return string|null
     */
    public function getRight(): ?string;

    /**
     * 表别名.
     *
     * @return string|null
     */
    public function getTableAlias(): ?string;

    /**
     * where条件.
     *
     * @return \Imi\Db\Query\Interfaces\IBaseWhere|null
     */
    public function getWhere(): ?IBaseWhere;

    /**
     * join类型，默认inner.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * 设置表名.
     *
     * @param string|null $table
     *
     * @return void
     */
    public function setTable(?string $table = null);

    /**
     * 设置在 join b on a.id=b.id 中的 a.id.
     *
     * @param string|null $left
     *
     * @return void
     */
    public function setLeft(?string $left);

    /**
     * 设置在 join b on a.id=b.id 中的 =.
     *
     * @param string|null $operation
     *
     * @return void
     */
    public function setOperation(?string $operation);

    /**
     * 设置join b on a.id=b.id 中的 b.id.
     *
     * @param string|null $right
     *
     * @return void
     */
    public function setRight(?string $right);

    /**
     * 设置表别名.
     *
     * @param string|null $tableAlias
     *
     * @return void
     */
    public function setTableAlias(?string $tableAlias);

    /**
     * 设置where条件.
     *
     * @param IBaseWhere|null $where
     *
     * @return void
     */
    public function setWhere(?IBaseWhere $where);

    /**
     * 设置join类型.
     *
     * @param string $type
     *
     * @return void
     */
    public function setType(string $type);
}
