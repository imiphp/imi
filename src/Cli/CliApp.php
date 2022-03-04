<?php

declare(strict_types=1);

namespace Imi\Cli;

use Imi\App;
use Imi\Bean\Scanner;
use Imi\Config;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Util\Imi;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\System;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CliApp extends BaseApp
{
    protected Application $cli;

    protected EventDispatcher $cliEventDispatcher;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        App::set(ProcessAppContexts::SCRIPT_NAME, realpath($_SERVER['SCRIPT_FILENAME']));
        $this->cliEventDispatcher = $dispatcher = new EventDispatcher();
        $this->cli = $cli = new Application('imi', App::getImiPrettyVersion());
        $cli->setDispatcher($dispatcher);
        $cli->setCatchExceptions(false);

        $definition = $cli->getDefinition();
        $definition->addOption(
            new InputOption(
                'app-namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Your project app namespace'
            )
        );
        $definition->addOption(
            new InputOption(
                'imi-runtime',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set imi runtime file',
                null,
            )
        );
        $definition->addOption(
            new InputOption(
                'app-runtime',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set app runtime file',
                null,
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function loadRuntime(): int
    {
        $this->initRuntime();
        $input = ImiCommand::getInput();
        // 尝试加载项目运行时
        $appRuntimeFile = $input->getParameterOption('--app-runtime');
        if (false !== $appRuntimeFile && Imi::loadRuntimeInfo($appRuntimeFile))
        {
            return LoadRuntimeResult::ALL;
        }
        // 尝试加载 imi 框架运行时
        if ($file = $input->getParameterOption('--imi-runtime'))
        {
            // 尝试加载指定 runtime
            $result = Imi::loadRuntimeInfo($file, true);
        }
        else
        {
            // 尝试加载默认 runtime
            $result = Imi::loadRuntimeInfo(Imi::getCurrentModeRuntimePath('imi-runtime'), true);
        }
        if (!$result)
        {
            // 不使用缓存时去扫描
            Scanner::scanImi();

            return LoadRuntimeResult::IMI_LOADED;
        }

        // @phpstan-ignore-next-line
        return $result ? LoadRuntimeResult::ALL : 0;
    }

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        parent::init();
        $this->addCommands();
    }

    private function addCommands(): void
    {
        foreach (CliManager::getCommands() as $command)
        {
            $command = new ImiCommand(
                $command['commandName'],
                $command['actionName'],
                $command['className'],
                $command['methodName'],
                $command['dynamicOptions'],
                $command['separator'] ?? '/'
            );
            if (!$this->cli->has($command->getName()))
            {
                $this->cli->add($command);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return 'cli';
    }

    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
        try
        {
            $this->cli->run(ImiCommand::getInput(), ImiCommand::getOutput());
        }
        catch (\Exception $th)
        {
            /** @var \Imi\Log\ErrorLog $errorLog */
            $errorLog = App::getBean('ErrorLog');
            $errorLog->onException($th);
            exit(255);
        }
    }

    public function getCli(): Application
    {
        return $this->cli;
    }

    /**
     * {@inheritDoc}
     */
    protected function initLogger(): void
    {
        $config = Config::get('@app.logger.channels.imi');
        if (null === $config)
        {
            Config::set('@app.logger.channels.imi', [
                'handlers' => [
                    [
                        'class'     => \Imi\Log\Handler\ConsoleHandler::class,
                        'formatter' => [
                            'class'     => \Imi\Log\Formatter\ConsoleLineFormatter::class,
                            'construct' => [
                                'format'                     => null,
                                'dateFormat'                 => 'Y-m-d H:i:s',
                                'allowInlineLineBreaks'      => true,
                                'ignoreEmptyContextAndExtra' => true,
                            ],
                        ],
                    ],
                ],
            ]);
        }
    }

    public static function printImi(): void
    {
        $output = ImiCommand::getOutput();
        $output->write('<comment>' . <<<'STR'
         _               _
        (_)  _ __ ___   (_)
        | | | '_ ` _ \  | |
        | | | | | | | | | |
        |_| |_| |_| |_| |_|

        </comment>
        STR
        );
    }

    public static function printEnvInfo(string $serverName, string $serverVer): void
    {
        $output = ImiCommand::getOutput();
        $output->writeln('<fg=yellow;options=bold>[System]</>');
        $system = (\defined('PHP_OS_FAMILY') && 'Unknown' !== \PHP_OS_FAMILY) ? \PHP_OS_FAMILY : \PHP_OS;

        switch ($system)
        {
            case 'Linux':
                $system .= ' - ' . Imi::getLinuxVersion();
                break;
            case 'Darwin':
                $system .= ' - ' . Imi::getDarwinVersion();
                break;
            case 'CYGWIN':
                $system .= ' - ' . Imi::getCygwinVersion();
                break;
        }
        $output->writeln('<info>System:</info> ' . $system);
        if (Imi::isDockerEnvironment())
        {
            $output->writeln('<info>Virtual machine:</info> Docker');
        }
        elseif (Imi::isWSL())
        {
            $output->writeln('<info>Virtual machine:</info> WSL');
        }
        $output->writeln('<info>CPU:</info> ' . System::getCpuCoresNum() . ' Cores');
        $output->writeln('<info>Disk:</info> Free ' . Imi::formatByte(@disk_free_space('.'), 3) . ' / Total ' . Imi::formatByte(@disk_total_space('.'), 3));

        if ($netIp = System::netLocalIp())
        {
            $output->writeln(\PHP_EOL . '<fg=yellow;options=bold>[Network]</>');
            foreach ($netIp as $name => $ip)
            {
                $output->writeln('<info>' . $name . '</info>: ' . $ip);
            }
        }

        $output->writeln(\PHP_EOL . '<fg=yellow;options=bold>[PHP]</>');
        $output->writeln('<info>Version:</info> v' . \PHP_VERSION);
        $output->writeln("<info>{$serverName}:</info> v{$serverVer}");
        $output->writeln('<info>imi:</info> ' . App::getImiPrettyVersion());
        $output->writeln('<info>Timezone:</info> ' . date_default_timezone_get());
        $output->writeln('<info>Opcache:</info> ' . Imi::getOpcacheInfo());

        $output->writeln('');
    }
}
