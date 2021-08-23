<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\SoftDelete\Annotation\SoftDelete;
use Imi\Model\SoftDelete\Traits\TSoftDelete;
use Imi\Pgsql\Test\Model\Base\TestSoftDeleteBase;

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
