<?php

namespace Imi\AC\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * ac_operation 基类.
 *
 * @Entity
 * @Table(name="ac_operation", id={"id"})
 * @DDL("CREATE TABLE `ac_operation` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID，顶级为0',   `index` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序，越小越靠前',   `code` varchar(32) NOT NULL COMMENT '操作代码',   `name` varchar(32) NOT NULL COMMENT '操作名称',   `description` text NOT NULL COMMENT '操作介绍',   PRIMARY KEY (`id`),   UNIQUE KEY `code` (`code`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 *
 * @property int    $id
 * @property int    $parentId    父级ID，顶级为0
 * @property int    $index       排序，越小越靠前
 * @property string $code        操作代码
 * @property string $name        操作名称
 * @property string $description 操作介绍
 */
abstract class OperationBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     *
     * @var int
     */
    protected $id;

    /**
     * 获取 id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int $id id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * 父级ID，顶级为0
     * parent_id.
     *
     * @Column(name="parent_id", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $parentId;

    /**
     * 获取 parentId - 父级ID，顶级为0.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * 赋值 parentId - 父级ID，顶级为0.
     *
     * @param int $parentId parent_id
     *
     * @return static
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * 排序，越小越靠前
     * index.
     *
     * @Column(name="index", type="tinyint", length=3, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $index;

    /**
     * 获取 index - 排序，越小越靠前.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * 赋值 index - 排序，越小越靠前.
     *
     * @param int $index index
     *
     * @return static
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * 操作代码
     * code.
     *
     * @Column(name="code", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $code;

    /**
     * 获取 code - 操作代码
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 赋值 code - 操作代码
     *
     * @param string $code code
     *
     * @return static
     */
    public function setCode($code)
    {
        if (\is_string($code) && mb_strlen($code) > 32)
        {
            throw new \InvalidArgumentException('The maximum length of $code is 32');
        }
        $this->code = $code;

        return $this;
    }

    /**
     * 操作名称
     * name.
     *
     * @Column(name="name", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $name;

    /**
     * 获取 name - 操作名称.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 赋值 name - 操作名称.
     *
     * @param string $name name
     *
     * @return static
     */
    public function setName($name)
    {
        if (\is_string($name) && mb_strlen($name) > 32)
        {
            throw new \InvalidArgumentException('The maximum length of $name is 32');
        }
        $this->name = $name;

        return $this;
    }

    /**
     * 操作介绍
     * description.
     *
     * @Column(name="description", type="text", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $description;

    /**
     * 获取 description - 操作介绍.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 赋值 description - 操作介绍.
     *
     * @param string $description description
     *
     * @return static
     */
    public function setDescription($description)
    {
        if (\is_string($description) && mb_strlen($description) > 65535)
        {
            throw new \InvalidArgumentException('The maximum length of $description is 65535');
        }
        $this->description = $description;

        return $this;
    }
}
