<?php
namespace Imi\Test\Component\Facade;

use Imi\Facade\BaseFacade;
use Imi\Facade\Annotation\Facade;

/**
 * @Facade("FacadeA")
 * @method mixed add($a, $b)
 */
abstract class FacadeA extends BaseFacade
{

}
