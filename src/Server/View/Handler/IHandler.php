<?php

declare(strict_types=1);

namespace Imi\Server\View\Handler;

use Imi\Server\Http\Message\Response;

interface IHandler
{
    /**
     * @param array|object $data
     */
    public function handle($data, array $options, Response $response): Response;
}
