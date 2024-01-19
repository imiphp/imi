<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\SoftDelete\Annotation\SoftDelete;
use Imi\Model\SoftDelete\Traits\TSoftDelete;
use Imi\Model\Test\Model\Base\PrefixBase;

/**
 * prefix.
 */
#[Inherit]
#[SoftDelete(field: 'delete_time')]
class Prefix extends PrefixBase
{
    use TSoftDelete;
}
