<?php
namespace Imi\Server\Http\Error;

interface IErrorHandler
{
	public function handle(\Throwable $throwable): bool;
}