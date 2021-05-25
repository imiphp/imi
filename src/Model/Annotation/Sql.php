<?php

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 字段 SQL 语句定义.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class Sql extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'sql';

    /**
     * SQL 语句.
     *
     * @var string
     */
    public $sql;
}
