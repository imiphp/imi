<?php
namespace Imi\Config\Dotenv;

use Dotenv\Dotenv as DotenvDotenv;
use Imi\Util\File;

class Dotenv extends DotenvDotenv
{
    /**
     * 路径数组
     *
     * @var string[]
     */
    private $paths;

    public function __construct($paths)
    {
        parent::__construct('');
        $this->paths = $paths;
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
    }

}