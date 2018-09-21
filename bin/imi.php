<?php
use Imi\App;
use Imi\Util\Args;
use Imi\Util\File;

Args::init();

$namespace = Args::get('appNamespace');
if(null === $namespace)
{
    $config = include File::path(dirname($_SERVER['SCRIPT_NAME'], 2), 'config/config.php');
    if(!isset($config['namespace']))
    {
        echo 'has no namespace, please add arg: -appNamespace "Your App Namespace"', PHP_EOL;
        exit;
    }
    $namespace = $config['namespace'];
}

App::run($namespace);
