<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 视图配置注解基类.
 *
 * @Parser("Imi\Bean\Parser\NullParser")
 */
abstract class BaseViewOption extends Base
{
}
