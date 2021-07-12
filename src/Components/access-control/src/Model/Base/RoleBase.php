<?php

namespace Imi\AC\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * ac_role 基类.
 *
 * @Entity
 * @Table(name="ac_role", id={"id"})
 * @DDL("CREATE TABLE `ac_role` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `code` varchar(32) NOT NULL COMMENT '角色代码',   `name` varchar(32) NOT NULL COMMENT '角色名称',   `description` text NOT NULL COMMENT '角色介绍',   PRIMARY KEY (`id`),   UNIQUE KEY `code` (`code`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 *
 * @property int    $id
 * @property string $code        角色代码
 * @property string $name        角色名称
 * @property string $description 角色介绍
 */
abstract class RoleBase extends Model
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
     * 角色代码
     * code.
     *
     * @Column(name="code", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $code;

    /**
     * 获取 code - 角色代码
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 赋值 code - 角色代码
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
     * 角色名称
     * name.
     *
     * @Column(name="name", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $name;

    /**
     * 获取 name - 角色名称.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 赋值 name - 角色名称.
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
     * 角色介绍
     * description.
     *
     * @Column(name="description", type="text", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $description;

    /**
     * 获取 description - 角色介绍.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 赋值 description - 角色介绍.
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
