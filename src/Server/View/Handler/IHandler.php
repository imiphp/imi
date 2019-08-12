<?php
namespace Imi\Server\View\Handler;

use Imi\Server\View\Annotation\View;
use Imi\Server\Http\Message\Response;

interface IHandler
{
    public function handle($data, array $options, Response $response): Response;
}