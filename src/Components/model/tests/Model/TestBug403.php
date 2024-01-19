<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Test\Model\Base\TestJsonBase;

/**
 * tb_test_json.
 *
 * @property \Imi\Util\LazyArrayObject|array $jsonData json数据
 */
#[Inherit]
#[Entity(camel: false)]
class TestBug403 extends TestJsonBase
{
    /**
     * json数据.
     * json_data.
     *
     * @var \Imi\Util\LazyArrayObject|object|array|null
     */
    #[Inherit]
    #[Serializable(allow: false)]
    protected $jsonData = null;
}
