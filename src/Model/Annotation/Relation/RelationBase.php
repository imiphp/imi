<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 关系注解基类.
 *
 * @Parser("Imi\Bean\Parser\NullParser")
 */
abstract class RelationBase extends Base
{
}
