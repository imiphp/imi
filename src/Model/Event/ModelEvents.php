<?php

namespace Imi\Model\Event;

abstract class ModelEvents
{
    /**
     * 初始化值前.
     */
    const BEFORE_INIT = 'BeforeInit';

    /**
     * 初始化值后.
     */
    const AFTER_INIT = 'AfterInit';

    /**
     * 插入前.
     */
    const BEFORE_INSERT = 'BeforeInsert';

    /**
     * 插入后.
     */
    const AFTER_INSERT = 'AfterInsert';

    /**
     * 更新前.
     */
    const BEFORE_UPDATE = 'BeforeUpdate';

    /**
     * 更新后.
     */
    const AFTER_UPDATE = 'AfterUpdate';

    /**
     * 删除前.
     */
    const BEFORE_DELETE = 'BeforeDelete';

    /**
     * 删除后.
     */
    const AFTER_DELETE = 'AfterDelete';

    /**
     * 保存前，先于插入前和更新前触发.
     */
    const BEFORE_SAVE = 'BeforeSave';

    /**
     * 保存后，后于插入后和更新后触发.
     */
    const AFTER_SAVE = 'AfterSave';

    /**
     * 批量更新前.
     */
    const BEFORE_BATCH_UPDATE = 'BeforeBatchUpdate';

    /**
     * 批量更新后.
     */
    const AFTER_BATCH_UPDATE = 'AfterBatchUpdate';

    /**
     * 批量删除前.
     */
    const BEFORE_BATCH_DELETE = 'BeforeBatchDelete';

    /**
     * 批量删除后.
     */
    const AFTER_BATCH_DELETE = 'AfterBatchDelete';

    /**
     * 查找前.
     */
    const BEFORE_FIND = 'BeforeFind';

    /**
     * 查找后.
     */
    const AFTER_FIND = 'AfterFind';

    /**
     * 查询前.
     */
    const BEFORE_SELECT = 'BeforeSelect';

    /**
     * 查询后.
     */
    const AFTER_SELECT = 'AfterSelect';

    /**
     * 查询后事件
     * 无论是Find、Select，还是通过Model::query()查询，都会触发该事件.
     */
    const AFTER_QUERY = 'AfterQuery';

    /**
     * 处理 save、insert、update 数据前.
     */
    const BEFORE_PARSE_DATA = 'BeforeParseData';

    /**
     * 处理 save、insert、update 数据后.
     */
    const AFTER_PARSE_DATA = 'AfterParseData';
}
