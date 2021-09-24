<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * test 基类.
 *
 * @Entity
 * @Table(name="tb_test_json", id={"id"})
 *
 * @property int|null                             $id
 * @property \Imi\Util\LazyArrayObject|array|null $jsonData json数据
 */
abstract class TestJsonBase extends Model
{
    /**
     * id.

     *
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=true, ndims=0)
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
    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * json数据.
     * json_data.
     *
     * @Column(name="json_data", type="json", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     *
     * @var \Imi\Util\LazyArrayObject|array|null
     */
    protected $jsonData = null;

    /**
     * 获取 jsonData - json数据.
     *
     * @return \Imi\Util\LazyArrayObject|array|null
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * 赋值 jsonData - json数据.
     *
     * @param \Imi\Util\LazyArrayObject|array|null $jsonData json_data
     *
     * @return static
     */
    public function setJsonData($jsonData)
    {
        $this->jsonData = $jsonData;

        return $this;
    }
}
