<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 字段 SQL 语句定义.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string $sql SQL 语句
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Sql extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'sql';

    public function __construct(?array $__data = null, string $sql = '')
    {
        parent::__construct(...\func_get_args());
    }
}
