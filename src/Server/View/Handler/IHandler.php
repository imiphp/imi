<?php

declare(strict_types=1);

namespace Imi\Server\View\Handler;

use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\View;

interface IHandler
{
    /**
     * @param mixed $data
     */
    public function handle(View $viewAnnotation, $data, IHttpResponse $response): IHttpResponse;
}
