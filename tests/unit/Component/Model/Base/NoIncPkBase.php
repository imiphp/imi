<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model;

/**
 * tb_no_inc_pk 基类.
 *
 * @Entity(camel=true, bean=true, incrUpdate=false)
 *
 * @Table(name="tb_no_inc_pk", usePrefix=false, id={"a_id", "b_id"}, dbPoolName=null)
 *
 * @DDL(sql="CREATE TABLE `tb_no_inc_pk` (   `a_id` int(10) unsigned NOT NULL,   `b_id` int(10) unsigned NOT NULL,   `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,   PRIMARY KEY (`a_id`,`b_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")
 *
 * @property int|null    $aId
 * @property int|null    $bId
 * @property string|null $value
 */
abstract class NoIncPkBase extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = 'a_id';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = ['a_id', 'b_id'];

    /**
     * a_id.
     *
     * @Column(name="a_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=false, unsigned=true, virtual=false)
     */
    protected ?int $aId = null;

    /**
     * 获取 aId.
     */
    public function getAId(): ?int
    {
        return $this->aId;
    }

    /**
     * 赋值 aId.
     *
     * @param int|null $aId a_id
     *
     * @return static
     */
    public function setAId($aId)
    {
        $this->aId = null === $aId ? null : (int) $aId;

        return $this;
    }

    /**
     * b_id.
     *
     * @Column(name="b_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false, unsigned=true, virtual=false)
     */
    protected ?int $bId = null;

    /**
     * 获取 bId.
     */
    public function getBId(): ?int
    {
        return $this->bId;
    }

    /**
     * 赋值 bId.
     *
     * @param int|null $bId b_id
     *
     * @return static
     */
    public function setBId($bId)
    {
        $this->bId = null === $bId ? null : (int) $bId;

        return $this;
    }

    /**
     * value.
     *
     * @Column(name="value", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false, virtual=false)
     */
    protected ?string $value = null;

    /**
     * 获取 value.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * 赋值 value.
     *
     * @param string|null $value value
     *
     * @return static
     */
    public function setValue($value)
    {
        if (\is_string($value) && mb_strlen($value) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $value is 255');
        }
        $this->value = null === $value ? null : (string) $value;

        return $this;
    }
}
