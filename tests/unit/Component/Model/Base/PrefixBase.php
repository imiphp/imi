<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Model;

/**
 * prefix 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property string|null $name
 * @property int|null    $deleteTime
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'prefix', dbPoolName: 'dbPrefix', id: [
        'id',
    ], usePrefix: true),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_prefix` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `name` varchar(255) NOT NULL,   `delete_time` int(10) unsigned NOT NULL DEFAULT \'0\',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1')
]
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
     */
    #[
        \Imi\Model\Annotation\Column(name: 'id', type: 'int', length: 10, nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true, unsigned: true)
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

    /**
     * delete_time.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'delete_time', type: 'int', length: 10, nullable: false, default: '0', unsigned: true)
    ]
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
    public function setDeleteTime(mixed $deleteTime): self
    {
        $this->deleteTime = null === $deleteTime ? null : (int) $deleteTime;

        return $this;
    }
}
