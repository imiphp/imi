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
 * test 基类.
 *
 * @Entity
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestJson.name", default="tb_test_json"), id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestJson.poolName"))
 * @DDL(sql="CREATE TABLE `tb_test_json` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `json_data` json NOT NULL COMMENT 'json数据',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='test'", decode="")
 *
 * @property int|null                                    $id
 * @property \Imi\Util\LazyArrayObject|object|array|null $jsonData json数据
 */
abstract class TestJsonBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
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
     * @Column(name="json_data", type="json", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var \Imi\Util\LazyArrayObject|object|array|null
     */
    protected $jsonData = null;

    /**
     * 获取 jsonData - json数据.
     *
     * @return \Imi\Util\LazyArrayObject|object|array|null
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * 赋值 jsonData - json数据.
     *
     * @param \Imi\Util\LazyArrayObject|object|array|null $jsonData json_data
     *
     * @return static
     */
    public function setJsonData($jsonData)
    {
        $this->jsonData = null === $jsonData ? null : $jsonData;

        return $this;
    }
}
