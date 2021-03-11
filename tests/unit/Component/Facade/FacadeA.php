<?php

namespace Imi\Test\Component\Facade;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @Facade("FacadeA")
 *
 * @method static mixed add($a, $b)
 */
abstract class FacadeA extends BaseFacade
{
}
