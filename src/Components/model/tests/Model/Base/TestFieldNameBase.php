<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_test_field_name 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property string|null $abcDef
 * @property string|null $aBCXYZ
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_test_field_name', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_test_field_name` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `Abc_Def` varchar(255) NOT NULL,   `ABC_XYZ` varchar(255) NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1')
]
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
     */
    #[
        \Imi\Model\Annotation\Column(name: 'id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true, unsigned: true)
    ]
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
    public function setId(mixed $id): self
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * Abc_Def.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'Abc_Def', type: 'varchar', length: 255, nullable: false)
    ]
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
    public function setAbcDef(mixed $abcDef): self
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
     */
    #[
        \Imi\Model\Annotation\Column(name: 'ABC_XYZ', type: 'varchar', length: 255, nullable: false)
    ]
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
    public function setABCXYZ(mixed $aBCXYZ): self
    {
        if (\is_string($aBCXYZ) && mb_strlen($aBCXYZ) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $aBCXYZ is 255');
        }
        $this->aBCXYZ = null === $aBCXYZ ? null : (string) $aBCXYZ;

        return $this;
    }
}
