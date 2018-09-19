<?php
namespace Imi\Server\Http\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface IHttpNotFoundHandler
{
    public function handle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}