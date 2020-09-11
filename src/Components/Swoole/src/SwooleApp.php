<?php

namespace Imi\Swoole;

use Imi\App;
use Imi\Cli\CliApp;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\OutputInterface;

class SwooleApp extends CliApp
{
    /**
     * 获取应用类型.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'swoole';
    }

    /**
     * 构造方法.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        $this->cliEventDispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $e) {
            $this->checkEnvironment($e->getOutput());
            App::set(ProcessAppContexts::PROCESS_NAME, ProcessType::MASTER, true);
            App::set(ProcessAppContexts::MASTER_PID, getmypid(), true);
        }, \PHP_INT_MAX - 1000);
    }

    /**
     * 检查环境.
     *
     * @return void
     */
    private function checkEnvironment(OutputInterface $output): void
    {
        // Swoole 检查
        if (!\extension_loaded('swoole'))
        {
            $output->writeln('<error>Swoole extension must be installed!</error>');
            $output->writeln('<info>Swoole Github:</info> <comment>https://github.com/swoole/swoole-src</comment>');
            exit;
        }
        // 短名称检查
        $useShortname = ini_get_all('swoole')['swoole.use_shortname']['local_value'];
        $useShortname = strtolower(trim(str_replace('0', '', $useShortname)));
        if (\in_array($useShortname, ['', 'off', 'false'], true))
        {
            $output->writeln('<error>Please enable swoole short name before using imi!</error>');
            $output->writeln('<info>You can set <comment>swoole.use_shortname = on</comment> into your php.ini.</info>');
            exit;
        }
    }
}
