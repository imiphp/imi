<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Test\Component\Model\Base\TestJsonBase;

/**
 * tb_test_json.
 *
 * @Inherit
 * @Entity(camel=false)
 *
 * @property \Imi\Util\LazyArrayObject|array $jsonData json数据
 */
class TestJsonNotCamel extends TestJsonBase
{
    /**
     * json数据
     * json_data.
     *
     * @Column(name="json_data", type="json", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $jsonData;
}
