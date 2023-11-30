<?php

declare(strict_types=1);

namespace Imi\Db\Transaction\Event;

use Imi\Db\Transaction\Transaction;
use Imi\Event\CommonEvent;

class CommitTransactionEvent extends CommonEvent
{
    public function __construct(string $__eventName,
        public readonly Transaction $transaction,
        public readonly int $level
    ) {
        parent::__construct($__eventName, $transaction);
    }
}
