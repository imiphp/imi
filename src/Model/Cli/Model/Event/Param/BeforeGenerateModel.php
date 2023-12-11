<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Model\Event\Param;

use Imi\Event\CommonEvent;

class BeforeGenerateModel extends CommonEvent
{
    use TGenerateModel;
}
