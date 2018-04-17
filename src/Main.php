<?php
namespace Imi;

use Imi\Server\Http\Server;
use Doctrine\Common\Annotations\AnnotationRegistry;

class Main
{
	public static function run()
	{
		// AnnotationRegistry::registerAutoloadNamespace('Imi\Server\Annotation', __DIR__);
		foreach(spl_autoload_functions() as $func)
		{
			AnnotationRegistry::registerLoader($func);
		}
		$server = new Server([]);
		$server->start();
	}
}
