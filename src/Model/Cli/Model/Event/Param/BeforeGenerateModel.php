<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeGenerateModel extends EventParam
{
    use TGenerateModel;
}
