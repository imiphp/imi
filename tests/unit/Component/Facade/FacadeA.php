<?php

declare(strict_types=1);

namespace Imi\Test\Component\Facade;

use Imi\Facade\Annotation\Facade;
use Imi\Facade\BaseFacade;

/**
 * @method static mixed add($a, $b)
 */
#[Facade(class: 'FacadeA')]
class FacadeA extends BaseFacade
{
}
