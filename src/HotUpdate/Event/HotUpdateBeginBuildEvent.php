<?php

declare(strict_types=1);

namespace Imi\HotUpdate\Event;

use Imi\Event\CommonEvent;

class HotUpdateBeginBuildEvent extends CommonEvent
{
    public bool $result = false;

    public function __construct(
        public readonly array $changedFiles,
        public readonly string $changedFilesFile,
    ) {
        parent::__construct(HotUpdateEvents::BEGIN_BUILD);
    }
}
