<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
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
}
