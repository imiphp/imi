<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Test\Model\Base\NoIncPkBase;

/**
 * tb_no_inc_pk.
 */
#[Inherit]
class NoIncPk extends NoIncPkBase
{
}
