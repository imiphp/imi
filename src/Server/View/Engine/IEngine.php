<?php

namespace Imi\Server\View\Engine;

use Imi\Server\Http\Message\Response;

interface IEngine
{
    public function render(Response $response, $fileName, $data = []): Response;
}
