<?php

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model;

/**
 * tb_test_json 基类.
 *
 * @Entity
 * @Table(name="tb_test_json", id={"id"})
 * @DDL("CREATE TABLE `tb_test_json` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `json_data` text NOT NULL COMMENT 'json数据',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 *
 * @property int    $id
 * @property string $jsonData json数据
 */
abstract class TestJsonBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     *
     * @var int
     */
    protected $id;

    /**
     * 获取 id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int $id id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * json数据
     * json_data.
     *
     * @Column(name="json_data", type="text", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $jsonData;

    /**
     * 获取 jsonData - json数据.
     *
     * @return string
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * 赋值 jsonData - json数据.
     *
     * @param string $jsonData json_data
     *
     * @return static
     */
    public function setJsonData($jsonData)
    {
        $this->jsonData = $jsonData;

        return $this;
    }
}
