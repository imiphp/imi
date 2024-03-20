<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Table\Event\Param;

use Imi\Event\CommonEvent;

class BeforeGenerateTable extends CommonEvent
{
    use TGenerateTable;
}
