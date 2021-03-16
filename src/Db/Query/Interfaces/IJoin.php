<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IJoin extends IBase
{
    /**
     * 表名.
     */
    public function getTable(): ?string;

    /**
     * 在 join b on a.id=b.id 中的 a.id.
     */
    public function getLeft(): ?string;

    /**
     * 在 join b on a.id=b.id 中的 =.
     */
    public function getOperation(): ?string;

    /**
     * join b on a.id=b.id 中的 b.id.
     */
    public function getRight(): ?string;

    /**
     * 表别名.
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
     */
    public function getType(): string;

    /**
     * 设置表名.
     */
    public function setTable(?string $table = null): void;

    /**
     * 设置在 join b on a.id=b.id 中的 a.id.
     */
    public function setLeft(?string $left): void;

    /**
     * 设置在 join b on a.id=b.id 中的 =.
     */
    public function setOperation(?string $operation): void;

    /**
     * 设置join b on a.id=b.id 中的 b.id.
     */
    public function setRight(?string $right): void;

    /**
     * 设置表别名.
     */
    public function setTableAlias(?string $tableAlias): void;

    /**
     * 设置where条件.
     */
    public function setWhere(?IBaseWhere $where): void;

    /**
     * 设置join类型.
     */
    public function setType(string $type): void;
}
