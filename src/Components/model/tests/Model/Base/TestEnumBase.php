<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_test_enum 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property string|null $value1
 * @property string|null $value2
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_test_enum', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_test_enum` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `value1` enum(\'a\',\'b\',\'c\',\'\'\'test\'\'\') NOT NULL DEFAULT \'\'\'test\'\'\',   `value2` enum(\'1\',\'2\',\'3\') NOT NULL DEFAULT \'1\',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8')
]
abstract class TestEnumBase extends Model
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
     * value1.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'value1', type: 'enum', length: 0, nullable: false, default: '\'test\'')
    ]
    protected ?string $value1 = '\'test\'';

    /**
     * 获取 value1.
     */
    public function getValue1(): ?string
    {
        return $this->value1;
    }

    /**
     * 赋值 value1.
     *
     * @param string|null $value1 value1
     *
     * @return static
     */
    public function setValue1(mixed $value1): self
    {
        $this->value1 = null === $value1 ? null : (string) $value1;

        return $this;
    }

    /**
     * value2.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'value2', type: 'enum', length: 0, nullable: false, default: '1')
    ]
    protected ?string $value2 = '1';

    /**
     * 获取 value2.
     */
    public function getValue2(): ?string
    {
        return $this->value2;
    }

    /**
     * 赋值 value2.
     *
     * @param string|null $value2 value2
     *
     * @return static
     */
    public function setValue2(mixed $value2): self
    {
        $this->value2 = null === $value2 ? null : (string) $value2;

        return $this;
    }
}
