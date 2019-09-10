<?php

use Imi\Event\Event;
use Imi\Util\ImiPriority;

Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Db\Statement\Listener\RequestContextDestroy, 'handle'], ImiPriority::IMI_MIN - 10);
Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Db\Listener\RequestContextDestroy, 'handle'], ImiPriority::IMI_MIN - 20);
Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Pool\Listener\RequestContextDestroy, 'handle'], ImiPriority::IMI_MIN - 30);
