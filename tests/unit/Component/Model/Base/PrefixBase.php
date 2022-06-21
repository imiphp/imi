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
 * prefix 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Prefix.name", default="prefix"), usePrefix=true, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Prefix.poolName", default="dbPrefix"))
 * @DDL(sql="CREATE TABLE `tb_prefix` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `name` varchar(255) NOT NULL,   `delete_time` int(10) unsigned NOT NULL DEFAULT '0',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1", decode="")
 *
 * @property int|null    $id
 * @property string|null $name
 * @property int|null    $deleteTime
 */
abstract class PrefixBase extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = 'id';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = ['id'];

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
     * name.
     *
     * @Column(name="name", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?string $name = null;

    /**
     * 获取 name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 赋值 name.
     *
     * @param string|null $name name
     *
     * @return static
     */
    public function setName($name)
    {
        if (\is_string($name) && mb_strlen($name) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $name is 255');
        }
        $this->name = null === $name ? null : (string) $name;

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
