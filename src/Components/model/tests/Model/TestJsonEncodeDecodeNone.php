<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\JsonDecode;
use Imi\Model\Annotation\JsonEncode;
use Imi\Model\Test\Model\Base\TestJsonBase;

/**
 * tb_test_json.
 *
 * @property \Imi\Util\LazyArrayObject|array $jsonData json数据
 * @property int|null                        $userId
 * @property int|null                        $userId2
 */
#[Inherit]
#[Entity(camel: false)]
class TestJsonEncodeDecodeNone extends TestJsonBase
{
    #[Inherit]
    #[JsonEncode]
    #[JsonDecode(wrap: '')]
    protected $jsonData = null;
}
