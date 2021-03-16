<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 表注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Table extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 表名.
     */
    public ?string $name = null;

    /**
     * 数据库连接池名称.
     */
    public ?string $dbPoolName = null;

    /**
     * 主键，支持数组方式设置联合索引.
     *
     * @var string|array|null
     */
    public $id = null;

    public function __construct(?array $__data = null, ?string $name = null, ?string $dbPoolName = null)
    {
        parent::__construct(...\func_get_args());
    }
}
