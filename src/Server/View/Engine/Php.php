<?php

declare(strict_types=1);

namespace Imi\Server\View\Engine;

use Imi\Server\Http\Message\Contract\IHttpResponse;

/**
 * PHP原生模版引擎.
 */
class Php implements IEngine
{
    /**
     * @param mixed $data
     */
    public function render(IHttpResponse $response, string $fileName, $data = []): IHttpResponse
    {
        if (!is_file($fileName))
        {
            return $response;
        }
        $closure = function (string $__renderFileName, $__renderData) {
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
