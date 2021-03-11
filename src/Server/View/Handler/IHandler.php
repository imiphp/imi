<?php

namespace Imi\Server\View\Handler;

use Imi\Server\Http\Message\Response;

interface IHandler
{
    /**
     * @param array                             $data
     * @param array                             $options
     * @param \Imi\Server\Http\Message\Response $response
     *
     * @return \Imi\Server\Http\Message\Response
     */
    public function handle($data, array $options, Response $response): Response;
}
