<?php

namespace Imi\Config\Dotenv;

use Dotenv\Loader\Loader;
use Dotenv\Repository\RepositoryInterface;
use Dotenv\Store\StoreBuilder;

class Dotenv
{
    private function __construct()
    {
    }

    /**
     * @param array $paths
     *
     * @return void
     */
    public static function load(array $paths)
    {
        $repository = \Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(\Dotenv\Repository\Adapter\EnvConstAdapter::class)
            ->addWriter(\Dotenv\Repository\Adapter\PutenvAdapter::class)
            ->immutable()
            ->make();
        $dotenv = self::create($repository, $paths);
        $dotenv->safeLoad();
    }

    /**
     * Create a new dotenv instance.
     *
     * @param \Dotenv\Repository\RepositoryInterface $repository
     * @param string|string[]                        $paths
     * @param string|string[]|null                   $names
     * @param bool                                   $shortCircuit
     * @param string|null                            $fileEncoding
     *
     * @return \Dotenv\Dotenv
     */
    public static function create(RepositoryInterface $repository, $paths, $names = null, bool $shortCircuit = true, string $fileEncoding = null): \Dotenv\Dotenv
    {
        $builder = null === $names ? StoreBuilder::createWithDefaultName() : StoreBuilder::createWithNoNames();

        foreach ((array) $paths as $path)
        {
            $builder = $builder->addPath($path);
        }

        foreach ((array) $names as $name)
        {
            $builder = $builder->addName($name);
        }

        if ($shortCircuit)
        {
            $builder = $builder->shortCircuit();
        }

        return new \Dotenv\Dotenv($builder->fileEncoding($fileEncoding)->make(), new Parser(), new Loader(), $repository);
    }
}
