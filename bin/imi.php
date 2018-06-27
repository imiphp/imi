<?php
use Imi\App;
use Imi\Util\Args;
use Imi\Util\File;

Args::init();

$namespace = Args::get('namespace');
if(null === $namespace)
{
	$config = include File::path(dirname($_SERVER['SCRIPT_NAME'], 2), 'config/config.php');
	$namespace = $config['namespace'];
}

App::run($namespace);
