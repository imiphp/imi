<?php

namespace Imi\Server\View\Engine;

use Imi\Server\Http\Message\Response;

/**
 * PHP原生模版引擎.
 */
class Php implements IEngine
{
    public function render(Response $response, $fileName, $data = []): Response
    {
        if (!is_file($fileName))
        {
            return $response;
        }
        $closure = function ($__renderFileName, $__renderData) {
            if (\is_array($__renderData))
            {
                extract($__renderData);
            }
            include $__renderFileName;
        };
        ob_start();
        $closure($fileName, $data);

        $response->getBody()->write(ob_get_clean());

        return $response;
    }
}
