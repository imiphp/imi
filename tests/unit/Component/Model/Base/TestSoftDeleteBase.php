<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * tb_test_soft_delete 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestSoftDelete.name", default="tb_test_soft_delete"), usePrefix=true, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestSoftDelete.poolName"))
 * @DDL(sql="CREATE TABLE `tb_test_soft_delete` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `title` varchar(255) NOT NULL,   `delete_time` int(10) unsigned NOT NULL DEFAULT '0',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property int|null    $id
 * @property string|null $title
 * @property int|null    $deleteTime
 */
abstract class TestSoftDeleteBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true, unsigned=true)
     */
    protected ?int $id = null;

    /**
     * 获取 id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int|null $id id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * title.
     *
     * @Column(name="title", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?string $title = null;

    /**
     * 获取 title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * 赋值 title.
     *
     * @param string|null $title title
     *
     * @return static
     */
    public function setTitle($title)
    {
        if (\is_string($title) && mb_strlen($title) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $title is 255');
        }
        $this->title = null === $title ? null : (string) $title;

        return $this;
    }

    /**
     * delete_time.
     *
     * @Column(name="delete_time", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $deleteTime = 0;

    /**
     * 获取 deleteTime.
     */
    public function getDeleteTime(): ?int
    {
        return $this->deleteTime;
    }

    /**
     * 赋值 deleteTime.
     *
     * @param int|null $deleteTime delete_time
     *
     * @return static
     */
    public function setDeleteTime($deleteTime)
    {
        $this->deleteTime = null === $deleteTime ? null : (int) $deleteTime;

        return $this;
    }
}
