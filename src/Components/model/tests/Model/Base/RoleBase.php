<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_role 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property string|null $name
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_role', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_role` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `name` varchar(255) NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8; insert into `tb_role` values(1,\'a\'); insert into `tb_role` values(2,\'b\'); insert into `tb_role` values(3,\'c\'); insert into `tb_role` values(4,\'d\'); insert into `tb_role` values(5,\'e\'); ')
]
abstract class RoleBase extends Model
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
     * name.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'name', type: 'varchar', length: 255, nullable: false)
    ]
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
    public function setName(mixed $name): self
    {
        if (\is_string($name) && mb_strlen($name) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $name is 255');
        }
        $this->name = null === $name ? null : (string) $name;

        return $this;
    }
}
