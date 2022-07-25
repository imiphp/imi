<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Entity;
use Imi\Test\Component\Model\Base\TestFieldNameBase;

/**
 * tb_test_field_name.
 *
 * @Inherit
 * @Entity(camel=false, bean=true)
 */
class TestFieldNameNotCamel extends TestFieldNameBase
{
}
