<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 创建表语句注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class DDL extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'sql';

    /**
     * 表结构 SQL.
     *
     * CREATE TABLE 语句
     *
     * @var string
     */
    public string $sql = '';

    public function __construct(?array $__data = null, string $sql = '')
    {
        parent::__construct(...\func_get_args());
    }
}
