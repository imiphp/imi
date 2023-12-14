<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Test\Model\Base\TestEnumBase;

/**
 * tb_test_enum.
 */
#[Inherit]
class TestEnum extends TestEnumBase
{
}
