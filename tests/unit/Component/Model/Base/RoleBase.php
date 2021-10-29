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
 * tb_role 基类.
 *
 * @Entity
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Role.name", default="tb_role"), id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Role.poolName"))
 * @DDL(sql="CREATE TABLE `tb_role` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `name` varchar(255) NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property int|null    $id
 * @property string|null $name
 */
abstract class RoleBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
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
     * @Column(name="name", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
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
}
