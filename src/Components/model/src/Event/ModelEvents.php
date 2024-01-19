<?php

declare(strict_types=1);

namespace Imi\Model\Event;

final class ModelEvents
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 初始化值前.
     */
    public const BEFORE_INIT = 'before.init';

    /**
     * 初始化值后.
     */
    public const AFTER_INIT = 'after.init';

    /**
     * 插入前.
     */
    public const BEFORE_INSERT = 'before.insert';

    /**
     * 插入后.
     */
    public const AFTER_INSERT = 'after.insert';

    /**
     * 更新前.
     */
    public const BEFORE_UPDATE = 'before.update';

    /**
     * 更新后.
     */
    public const AFTER_UPDATE = 'after.update';

    /**
     * 删除前.
     */
    public const BEFORE_DELETE = 'before.delete';

    /**
     * 删除后.
     */
    public const AFTER_DELETE = 'after.delete';

    /**
     * 保存前，先于插入前和更新前触发.
     */
    public const BEFORE_SAVE = 'before.save';

    /**
     * 保存后，后于插入后和更新后触发.
     */
    public const AFTER_SAVE = 'after.save';

    /**
     * 查找前.
     */
    public const BEFORE_FIND = 'before.find';

    /**
     * 查找后.
     */
    public const AFTER_FIND = 'after.find';

    /**
     * 查询前.
     */
    public const BEFORE_SELECT = 'before.select';

    /**
     * 查询后.
     */
    public const AFTER_SELECT = 'after.select';

    /**
     * 查询后事件
     * 无论是find、select，还是通过Model::query()查询，都会触发该事件.
     */
    public const AFTER_QUERY = 'after.query';

    /**
     * 处理 save、insert、update 数据前.
     */
    public const BEFORE_PARSE_DATA = 'before.parse_data';

    /**
     * 处理 save、insert、update 数据后.
     */
    public const AFTER_PARSE_DATA = 'after.parse_data';
}
