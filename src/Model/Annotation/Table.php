<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 表注解.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @property string|null       $name       表名
 * @property string|null       $dbPoolName 数据库连接池名称
 * @property string|array|null $id         主键，支持数组方式设置联合索引
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Table extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * @param string|array|null $id
     */
    public function __construct(?array $__data = null, ?string $name = null, ?string $dbPoolName = null, $id = null)
    {
        parent::__construct(...\func_get_args());
    }
}
