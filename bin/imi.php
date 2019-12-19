<?php
use Imi\App;
use Imi\Util\Args;
use Imi\Util\File;

$imi = new class{
    public function run()
    {
        if(!class_exists('Imi\App'))
        {
            $paths = [
                $_SERVER['PWD'] ?? getcwd(),
                dirname(__DIR__),
            ];
            foreach($paths as $path)
            {
                $fileName = $path . '/vendor/autoload.php';
                if(is_file($fileName))
                {
                    break;
                }
            }
            if(!is_file($fileName))
            {
                echo 'No file vendor/autoload.php', PHP_EOL;
                exit;
            }
            $loader = require $fileName;
            App::setLoader($loader);
            unset($fileName, $loader);
        }

        Args::init(1);

        $namespace = Args::get('appNamespace');
        if(null === $namespace)
        {
            $config = include File::path($path ?? dirname($_SERVER['SCRIPT_NAME'], 2), 'config/config.php');
            if(!isset($config['namespace']))
            {
                echo 'Has no namespace, please add arg: -appNamespace "Your App Namespace"', PHP_EOL;
                exit;
            }
            $namespace = $config['namespace'];
            unset($config);
        }

        App::run($namespace);
    }
};
$imi->run();
