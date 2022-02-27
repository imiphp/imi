<?php

declare(strict_types=1);

namespace Imi\Phar;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PharBuildCommand extends Command
{
    protected static $defaultName = 'phar:build';

    protected function configure()
    {
        $container = implode('、', Constant::CONTAINER_SET);
        $this
            ->addArgument('container', InputArgument::OPTIONAL, "支持容器, {$container}")
            ->addOption('init', null, InputOption::VALUE_NONE, '初始化配置文件')
            ->setDescription('构建 phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseDir = getcwd();
        $configFile = "{$baseDir}/" . Constant::CFG_FILE_NAME;
        $checkConfig = is_file($configFile);

        $output->writeln("project dir : <info>{$baseDir}</info>");
        $output->writeln(sprintf('check config: <info>%s</info>', $checkConfig ? '<info>success</info>' : '<comment>fail</comment>'));

        if ($input->getOption('init'))
        {
            // todo 文件存在情况下需要应答确认
            copy(__DIR__ . '/../config/imi-phar-cfg.php', $configFile);
            $output->writeln("write {$configFile} ...");
            $output->writeln('configuration file initialization completed.');

            return self::SUCCESS;
        }

        if (!$checkConfig)
        {
            $output->writeln('config file does not exist, execute "vendor/bin/imi-phar --init" initialize configuration file.');

            return self::INVALID;
        }

        try
        {
            $config = require $configFile;
        }
        catch (\Throwable $exception)
        {
            $output->writeln('config load fail: ' . $exception);

            return self::FAILURE;
        }

        if (!\is_array($config))
        {
            $output->writeln('config load fail: invalid config');

            return self::FAILURE;
        }

        $container = $input->getArgument('container');
        if (empty($container))
        {
            $output->writeln('invalid container value');

            return self::INVALID;
        }

        if (!\in_array($container, Constant::CONTAINER_SET))
        {
            $output->writeln('invalid container value');

            return self::INVALID;
        }

        // todo 支持自动禁用热更新

        $phar = new PharService(
            $output,
            $baseDir,
            $config,
        );
        $phar->build($container);

        return self::SUCCESS;
    }
}
