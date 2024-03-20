<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Table\Event\Param;

use Imi\Event\CommonEvent;

class AfterGenerateTable extends CommonEvent
{
    use TGenerateTable;
}
