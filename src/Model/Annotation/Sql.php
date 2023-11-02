<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 字段 SQL 语句定义.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Sql extends Base
{
    public function __construct(
        /**
         * SQL 语句.
         */
        public string $sql = ''
    ) {
    }
}
