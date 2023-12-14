<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Test\Model\Base\RoleBase;

/**
 * tb_role.
 */
#[Inherit]
class Role extends RoleBase
{
}
