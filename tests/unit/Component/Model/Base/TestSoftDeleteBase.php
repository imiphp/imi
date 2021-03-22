<?php

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * tb_test_soft_delete 基类.
 *
 * @Entity
 * @Table(name="tb_test_soft_delete", id={"id"})
 * @DDL("CREATE TABLE `tb_test_soft_delete` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `title` varchar(255) NOT NULL,   `delete_time` int(10) unsigned NOT NULL DEFAULT '0',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 *
 * @property int    $id
 * @property string $title
 * @property int    $deleteTime
 */
abstract class TestSoftDeleteBase extends Model
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
     * title.
     *
     * @Column(name="title", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $title;

    /**
     * 获取 title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * 赋值 title.
     *
     * @param string $title title
     *
     * @return static
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * delete_time.
     *
     * @Column(name="delete_time", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $deleteTime;

    /**
     * 获取 deleteTime.
     *
     * @return int
     */
    public function getDeleteTime()
    {
        return $this->deleteTime;
    }

    /**
     * 赋值 deleteTime.
     *
     * @param int $deleteTime delete_time
     *
     * @return static
     */
    public function setDeleteTime($deleteTime)
    {
        $this->deleteTime = $deleteTime;

        return $this;
    }
}
