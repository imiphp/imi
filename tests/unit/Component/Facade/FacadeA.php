<?php

namespace Imi\Test\Component\Facade;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @Facade("FacadeA")
 *
 * @method mixed add($a, $b)
 */
abstract class FacadeA extends BaseFacade
{
}
