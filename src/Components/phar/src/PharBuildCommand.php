<?php

declare(strict_types=1);

namespace Imi\Phar;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function getcwd;

class PharBuildCommand extends Command
{
    protected static $defaultName = 'phar:build';

    protected function configure()
    {
        $this
            ->addArgument('container', InputArgument::REQUIRED, '可选 swoole、workerman、roadrunner')
            ->setDescription('构建 phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $input->getArgument('container');


        $phar = new PharService(
            getcwd(),
            [
                'output'       => 'build/imi.phar',
                'dirs'         => [
                    'ApiServer',
                    'config',
                    "helpers",
                    'resource',
                ],
                'excludeDirs'  => [],
                'excludeFiles' => [],
                'files'        => [
                    "bootstrap.php",
                ],
                'vendorScan'   => true,
                'finder' => [],
            ]);
        $phar->build($container);

        return self::SUCCESS;
    }
}
