<?php
namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 列字段注解
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
class Column extends Base
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 字段名
     * @var string|array
     */
    public $name;

    /**
     * 字段类型
     * @var string
     */
    public $type;

    /**
     * 字段长度
     * @var integer
     */
    public $length = -1;

    /**
     * 是否允许为null
     * @var boolean
     */
    public $nullable = true;

    /**
     * 精度，小数位后几位
     * @var int
     */
    public $accuracy = 0;

    /**
     * 默认值
     * @var mixed
     */
    public $default;

    /**
     * 是否为主键
     * @var boolean
     */
    public $isPrimaryKey = false;

    /**
     * 联合主键中的第几个，从0开始
     * @var integer
     */
    public $primaryKeyIndex = -1;

    /**
     * 是否为自增字段
     * @var boolean
     */
    public $isAutoIncrement = false;

    /**
     * 虚拟字段，不参与数据库操作
     *
     * @var boolean
     */
    public $virtual = false;
}