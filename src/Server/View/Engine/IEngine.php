<?php

declare(strict_types=1);

namespace Imi\Server\View\Engine;

use Imi\Server\Http\Message\Contract\IHttpResponse;

interface IEngine
{
    public function render(IHttpResponse $response, string $fileName, array $data = []): IHttpResponse;
}
