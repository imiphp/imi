<?php
use Imi\App;
use Imi\Util\File;
use Symfony\Component\Console\Input\ArgvInput;

(function(){
    if(!class_exists('Imi\App'))
    {
        (function(){
            foreach([
                $_SERVER['PWD'] ?? getcwd(),
                dirname(__DIR__),
                dirname(__DIR__, 4), // 在非工作路径，使用绝对路径启动
            ] as $path)
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
            require $fileName;
        })();
    }

    App::run((function(){
        $input = new ArgvInput;
        $namespace = $input->getParameterOption('--app-namespace', false);
        if(false === $namespace)
        {
            $config = include File::path($path ?? dirname($_SERVER['SCRIPT_NAME'], 2), 'config/config.php');
            if(!isset($config['namespace']))
            {
                echo 'Has no namespace, please add arg: --app-namespace "Your App Namespace"', PHP_EOL;
                exit;
            }
            $namespace = $config['namespace'];
        }
        return $namespace;
    })(), \Imi\Swoole\SwooleApp::class);
})();
