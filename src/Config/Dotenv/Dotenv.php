<?php
namespace Imi\Config\Dotenv;

use Imi\App;
use Imi\Config;
use Imi\Util\Imi;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("Dotenv")
 */
class Dotenv extends \Dotenv\Dotenv
{
    public function __construct()
    {
        parent::__construct(Imi::getNamespacePath(App::getNamespace()));
    }

    public function init()
    {
        foreach($_ENV as $name => $value)
        {
            $this->loader->clearEnvironmentVariable($name);
        }
        $this->overload();
        foreach($_ENV as $name => $value)
        {
            Config::set($name, $value);
        }
    }
}