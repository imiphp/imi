<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Test\Model\Base\UnusedBase;

/**
 * tb_unused.
 */
#[Inherit]
class Unused extends UnusedBase
{
}
