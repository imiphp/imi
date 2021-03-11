<?php

namespace Imi\Server\View\Engine;

use Imi\Server\Http\Message\Response;

interface IEngine
{
    /**
     * @param \Imi\Server\Http\Message\Response $response
     * @param string                            $fileName
     * @param array                             $data
     *
     * @return \Imi\Server\Http\Message\Response
     */
    public function render(Response $response, $fileName, $data = []): Response;
}
