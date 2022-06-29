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
 * tb_virtual_column 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\VirtualColumn.name", default="tb_virtual_column"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\VirtualColumn.poolName"))
 * @DDL(sql="CREATE TABLE `tb_virtual_column` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `amount` int(11) NOT NULL,   `virtual_amount` decimal(10,2) GENERATED ALWAYS AS ((`amount` / 100)) VIRTUAL NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", decode="")
 *
 * @property int|null              $id
 * @property int|null              $amount
 * @property string|float|int|null $virtualAmount
 */
abstract class VirtualColumnBase extends Model
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
     * amount.
     *
     * @Column(name="amount", type="int", length=11, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false, virtual=false)
     */
    protected ?int $amount = null;

    /**
     * 获取 amount.
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * 赋值 amount.
     *
     * @param int|null $amount amount
     *
     * @return static
     */
    public function setAmount($amount)
    {
        $this->amount = null === $amount ? null : (int) $amount;

        return $this;
    }

    /**
     * virtual_amount.
     *
     * @Column(name="virtual_amount", type="decimal", length=10, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false, virtual=true)
     *
     * @var string|float|int|null
     */
    protected $virtualAmount = null;

    /**
     * 获取 virtualAmount.
     *
     * @return string|float|int|null
     */
    public function getVirtualAmount()
    {
        return $this->virtualAmount;
    }

    /**
     * 赋值 virtualAmount.
     *
     * @param string|float|int|null $virtualAmount virtual_amount
     *
     * @return static
     */
    public function setVirtualAmount($virtualAmount)
    {
        $this->virtualAmount = null === $virtualAmount ? null : $virtualAmount;

        return $this;
    }
}
