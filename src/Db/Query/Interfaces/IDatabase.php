<?php

namespace Imi\Db\Query\Interfaces;

interface IDatabase extends IBase
{
    /**
     * 获取数据库名.
     *
     * @return string
     */
    public function getDatabase(): string;

    /**
     * 设置数据库名.
     *
     * @param string $database
     *
     * @return void
     */
    public function setDatabase(string $database = null);

    /**
     * 获取别名.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * 设置别名.
     *
     * @param string $alias
     *
     * @return void
     */
    public function setAlias(string $alias = null);

    /**
     * 设置值，可以根据传入的值自动处理
     * name——database
     * name alias——database alias
     * name as alias——database as alias.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValue($value);
}
