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
            $command = new ImiCommand($command['commandName'], $command['actionName'], $command['className'], $command['methodName'], $command['dynamicOptions']);
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
}
