<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\SoftDelete\Annotation\SoftDelete;
use Imi\Model\SoftDelete\Traits\TSoftDelete;
use Imi\Test\Component\Model\Base\TestSoftDeleteBase;

/**
 * tb_test_soft_delete.
 *
 * @Inherit
 * @SoftDelete
 */
class TestSoftDelete extends TestSoftDeleteBase
{
    use TSoftDelete;
}
