<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * test 基类.
 *
 * @Entity(camel=true, bean=true, incrUpdate=false)
 *
 * @Table(name=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\TestJson.name", default="tb_test_json"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\TestJson.poolName"))
 *
 * @property int|null                             $id
 * @property \Imi\Util\LazyArrayObject|array|null $jsonData  json数据
 * @property \Imi\Util\LazyArrayObject|array|null $jsonbData jsonb数据
 */
abstract class TestJsonBase extends Model
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
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true, ndims=0, virtual=false)
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
     * json数据.
     * json_data.
     *
     * @Column(name="json_data", type="json", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     *
     * @var \Imi\Util\LazyArrayObject|array|null
     */
    protected $jsonData = null;

    /**
     * 获取 jsonData - json数据.
     *
     * @return \Imi\Util\LazyArrayObject|array|null
     */
    public function &getJsonData()
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
        $this->jsonData = null === $jsonData ? null : $jsonData;

        return $this;
    }

    /**
     * jsonb数据.
     * jsonb_data.
     *
     * @Column(name="jsonb_data", type="jsonb", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     *
     * @var \Imi\Util\LazyArrayObject|array|null
     */
    protected $jsonbData = null;

    /**
     * 获取 jsonbData - jsonb数据.
     *
     * @return \Imi\Util\LazyArrayObject|array|null
     */
    public function &getJsonbData()
    {
        return $this->jsonbData;
    }

    /**
     * 赋值 jsonbData - jsonb数据.
     *
     * @param \Imi\Util\LazyArrayObject|array|null $jsonbData jsonb_data
     *
     * @return static
     */
    public function setJsonbData($jsonbData)
    {
        $this->jsonbData = null === $jsonbData ? null : $jsonbData;

        return $this;
    }
}
