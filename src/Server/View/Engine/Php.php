<?php
namespace Imi\Server\View\Engine;

use Imi\Server\Http\Message\Response;
use Imi\Util\Call;

/**
 * PHP原生模版引擎
 */
class Php implements IEngine
{
	public function render(Response $response, $fileName, $data = []): Response
	{
		if(!is_file($fileName))
		{
			return $response;
		}
		$closure = function($__renderFileName, $__renderData){
			extract($__renderData);
			include $__renderFileName;
		};
		ob_start();
		Call::callUserFunc($closure, $fileName, $data);
		return $response->write(ob_get_clean());
	}
}