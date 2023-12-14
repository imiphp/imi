<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Entity;
use Imi\Model\Test\Model\Base\TestFieldNameBase;

/**
 * tb_test_field_name.
 */
#[Inherit]
#[Entity(camel: false)]
class TestFieldNameNotCamel extends TestFieldNameBase
{
}
