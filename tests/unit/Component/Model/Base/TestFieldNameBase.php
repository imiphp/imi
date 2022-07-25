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
 * tb_test_field_name 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestFieldName.name", default="tb_test_field_name"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestFieldName.poolName"))
 * @DDL(sql="CREATE TABLE `tb_test_field_name` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `Abc_Def` varchar(255) NOT NULL,   `ABC_XYZ` varchar(255) NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1", decode="")
 *
 * @property int|null    $id
 * @property string|null $abcDef
 * @property string|null $aBCXYZ
 */
abstract class TestFieldNameBase extends Model
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
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true, unsigned=true, virtual=false)
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
     * Abc_Def.
     *
     * @Column(name="Abc_Def", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false, virtual=false)
     */
    protected ?string $abcDef = null;

    /**
     * 获取 abcDef.
     */
    public function getAbcDef(): ?string
    {
        return $this->abcDef;
    }

    /**
     * 赋值 abcDef.
     *
     * @param string|null $abcDef Abc_Def
     *
     * @return static
     */
    public function setAbcDef($abcDef)
    {
        if (\is_string($abcDef) && mb_strlen($abcDef) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $abcDef is 255');
        }
        $this->abcDef = null === $abcDef ? null : (string) $abcDef;

        return $this;
    }

    /**
     * ABC_XYZ.
     *
     * @Column(name="ABC_XYZ", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false, virtual=false)
     */
    protected ?string $aBCXYZ = null;

    /**
     * 获取 aBCXYZ.
     */
    public function getABCXYZ(): ?string
    {
        return $this->aBCXYZ;
    }

    /**
     * 赋值 aBCXYZ.
     *
     * @param string|null $aBCXYZ ABC_XYZ
     *
     * @return static
     */
    public function setABCXYZ($aBCXYZ)
    {
        if (\is_string($aBCXYZ) && mb_strlen($aBCXYZ) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $aBCXYZ is 255');
        }
        $this->aBCXYZ = null === $aBCXYZ ? null : (string) $aBCXYZ;

        return $this;
    }
}
