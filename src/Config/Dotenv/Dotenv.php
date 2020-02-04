<?php
namespace Imi\Config\Dotenv;

use Dotenv\Dotenv as DotenvDotenv;
use Imi\App;
use Imi\Config;
use Imi\Util\Imi;
use Imi\Bean\Annotation\Bean;
use Imi\Util\File;

/**
 * @Bean("Dotenv")
 */
class Dotenv extends DotenvDotenv
{
    /**
     * 路径数组
     *
     * @var string[]
     */
    private $paths;

    public function __construct()
    {
        $this->paths = Imi::getNamespacePaths(App::getNamespace());
    }

    public function init()
    {
        foreach($_ENV as $name => $value)
        {
            $this->loader->clearEnvironmentVariable($name);
        }
        foreach($this->paths as $path)
        {
            $filePath = File::path($path, '.env');
            if(is_file($filePath))
            {
                $obj = new DotenvDotenv($path);
                $obj->overload();
            }
        }
        foreach($_ENV as $name => $value)
        {
            Config::set($name, $value);
        }
    }

    /**
     * Get 路径数组
     *
     * @return string[]
     */ 
    public function getPaths()
    {
        return $this->paths;
    }

}