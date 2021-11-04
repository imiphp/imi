<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 关系注解基类.
 *
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property bool $with 是否默认使用预加载特性
 */
abstract class RelationBase extends Base
{
}
