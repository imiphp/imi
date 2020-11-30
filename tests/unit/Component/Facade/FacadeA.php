<?php

declare(strict_types=1);

namespace Imi\Test\Component\Facade;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @Facade("FacadeA")
 *
 * @method mixed add($a, $b)
 */
class FacadeA extends BaseFacade
{
}
