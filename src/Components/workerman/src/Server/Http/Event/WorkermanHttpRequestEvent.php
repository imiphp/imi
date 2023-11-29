<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Event;

use Imi\Event\CommonEvent;
use Imi\Workerman\Http\Message\WorkermanRequest;
use Imi\Workerman\Http\Message\WorkermanResponse;
use Imi\Workerman\Server\Contract\IWorkermanServer;

class WorkermanHttpRequestEvent extends CommonEvent
{
    public function __construct(
        public readonly IWorkermanServer $server,
        public readonly WorkermanRequest $request,
        public readonly WorkermanResponse $response,
    ) {
        parent::__construct('IMI.WORKERMAN.SERVER.HTTP.REQUEST', $server);
    }
}
