<?php

namespace Imi\Config\Dotenv;

class Dotenv
{
    /**
     * 路径数组.
     *
     * @var string[]
     */
    private array $paths;

    /**
     * @var \Dotenv\Dotenv
     */
    private \Dotenv\Dotenv $dotenv;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
        $this->dotenv = $dotenv = \Dotenv\Dotenv::createImmutable($paths);
    }

    /**
     * 初始化.
     *
     * @return void
     */
    public function init(): void
    {
        $this->dotenv->load();
    }
}
