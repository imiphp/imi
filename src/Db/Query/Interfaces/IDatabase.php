<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IDatabase extends IBase
{
    /**
     * 获取数据库名.
     */
    public function getDatabase(): ?string;

    /**
     * 设置数据库名.
     */
    public function setDatabase(?string $database = null): void;

    /**
     * 获取别名.
     */
    public function getAlias(): ?string;

    /**
     * 设置别名.
     */
    public function setAlias(?string $alias = null): void;

    /**
     * 设置值，可以根据传入的值自动处理
     * name——database
     * name alias——database alias
     * name as alias——database as alias.
     */
    public function setValue(string $value): void;
}
