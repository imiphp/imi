<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\JsonDecode;
use Imi\Model\Annotation\JsonEncode;
use Imi\Test\Component\Model\Base\TestJsonBase;

/**
 * tb_test_json.
 *
 * @Inherit
 *
 * @Entity(camel=false)
 *
 * @property \Imi\Util\LazyArrayObject|array $jsonData json数据
 * @property int|null                        $userId
 * @property int|null                        $userId2
 */
class TestJsonEncodeDecodeCallable extends TestJsonBase
{
    /**
     * @Inherit
     *
     * @JsonEncode
     *
     * @JsonDecode(wrap="Imi\Test\Component\Model\parseJsonEncodeDecodeCallableData")
     */
    protected $jsonData = null;
}

function parseJsonEncodeDecodeCallableData(array $data): array
{
    return ['data' => $data];
}
