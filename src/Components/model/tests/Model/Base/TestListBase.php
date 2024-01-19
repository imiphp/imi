<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_test_list 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property string|null $list
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_test_list', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_test_list` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `list` varchar(255) NOT NULL DEFAULT \'\',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8')
]
abstract class TestListBase extends Model
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
     * list.
     *
     * @var string|null
     */
    #[
        \Imi\Model\Annotation\Column(name: 'list', type: 'varchar', length: 255, nullable: false, default: '')
    ]
    protected $list = '';

    /**
     * 获取 list.
     *
     * @return string|null
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * 赋值 list.
     *
     * @param string|null $list list
     *
     * @return static
     */
    public function setList(mixed $list): self
    {
        if (\is_string($list) && mb_strlen($list) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $list is 255');
        }
        $this->list = null === $list ? null : (string) $list;

        return $this;
    }
}
