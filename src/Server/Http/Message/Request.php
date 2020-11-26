<?php

namespace Imi\Server\Http\Message;

use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Util\Http\ServerRequest;

abstract class Request extends ServerRequest implements IHttpRequest
{
}
